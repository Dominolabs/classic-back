<?php


namespace app\module\api\module\viber\controllers\handlers\events;

use app\module\api\module\viber\controllers\helpers\FileHelper;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\senders\ViberSender;
use app\module\api\module\viber\exceptions\ValidationException;
use app\module\api\module\viber\jobs\MessageHandlerJob;
use Carbon\Carbon;
use Throwable;
use app\module\api\module\viber\controllers\handlers\ViberUserHandler;
use app\module\api\module\viber\interfaces\HandlerInterface;
use app\module\api\module\viber\models\ViberMessage;
use app\module\api\module\viber\models\ViberMessageViberUser;
use Yii;
use yii\db\StaleObjectException;

class MessageHandler extends EventHandler implements HandlerInterface
{
    /**
     * @param $data
     * @return array|null
     * @throws Throwable
     */
    public function run($data)
    {
       $id =  Yii::$app->queue->push(new MessageHandlerJob([
            'chat_id' => $this->chat->viber_chat_id,
            'data' => base64_encode(serialize($data)),
        ]));

        return $this->sendSuccessResponse();
    }

    /**
     * @param $data
     * @throws StaleObjectException
     * @throws Throwable
     * @throws ValidationException
     */
    public function work($data)
    {
        if (!empty($data['sender'])) {

            $this->v_u = (new ViberUserHandler($this->chat))->run([
                'user' => $data['sender'],
                'event' => 'message'
            ]);
            Yii::$app->language = $this->v_u->language;
            ViberSender::setLanguage($this->v_u);
            /** @var ViberMessage $v_m */
            $v_m = ViberMessage::where(['message_token' => $data['message_token']])->one(); //needed because viber sends several messages in a row if wrong answer from server
            $this->prepareData($data);
            if (!$v_m) {
                $v_m = ViberMessage::create($data);
                $v_m->link('viberUsers', $this->v_u, ['delivered_at' => Helper::now()->getTimestamp()]);
            } else {
                $v_m->update($data);
                $pivot = ViberMessageViberUser::where([
                    'viber_message_id' => $v_m->viber_message_id,
                    'viber_user_id' => $this->v_u->viber_user_id
                ])->one();
                if (!$pivot) $v_m->link('viberUsers', $this->v_u, ['delivered_at' => Helper::now()->getTimestamp()]);
            }
            $f_types = ['picture', 'video', 'file'];
            if (in_array($v_m->type, $f_types)) {
                $message = json_decode($v_m->message, true);
                if (is_array($message) && array_key_exists('media', $message))
                    FileHelper::saveFileFromLink($v_m, $message['media'], null, $message['file_name'] ?? null);
            }
            if (!empty($v_m->tracking_data)) $this->handleTrackingData($v_m);
        }
    }

    /**
     * @param array $data
     */
    private function prepareData(array &$data)
    {
        $data['sent_at'] = empty($data['timestamp']) ? Helper::now()->getTimestamp() :
            Carbon::createFromTimestampMs($data['timestamp'])->getTimestamp();

        $data['type'] = $data['message']['type'];
        $data['viber_chat_id'] = $this->chat->viber_chat_id;
        $data['message_type'] = 'received';
        $data['tracking_data'] = $data['message']['tracking_data'] ?? null;
        foreach ($data as $key => $value) {
            if (is_array($value)) $data[$key] = json_encode($value);
        }
    }

    /**
     * @param ViberMessage $v_m
     */
    private function handleTrackingData(ViberMessage $v_m)
    {
        $handlers = Helper::config('handlers.tracking_data');
        if (array_key_exists($v_m->tracking_data, $handlers)
            && ($handler = new $handlers[$v_m->tracking_data]($this->v_u)) instanceof HandlerInterface) {
            /** @var HandlerInterface $handler */
            $handler->run($v_m);
        }
    }
}