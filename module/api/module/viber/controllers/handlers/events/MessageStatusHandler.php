<?php


namespace app\module\api\module\viber\controllers\handlers\events;


use Carbon\Carbon;
use app\module\api\module\viber\exceptions\ValidationException;
use app\module\api\module\viber\interfaces\HandlerInterface;
use app\module\api\module\viber\models\ViberMessage;
use app\module\api\module\viber\models\ViberMessageViberUser;
use app\module\api\module\viber\models\ViberUser;

class MessageStatusHandler extends EventHandler implements HandlerInterface
{
    /**
     * @param $data
     * @return array|void|null
     * @throws ValidationException
     */
    public function run($data)
    {
        if (empty($data['message_token']) || empty($data['user_id'])) return;
        /** @var ViberMessage $message */
        $message = ViberMessage::where(['message_token' => $data['message_token']])->one();
        $this->v_u = ViberUser::where(['viber_id'  => $data['user_id']])->with('viberMessages')->one();

        if (!$message || !$this->v_u) return;
        $date = empty($data['timestamp']) ? now()->getTimestamp() :
            Carbon::createFromTimestampMs($data['timestamp'])->getTimestamp();

        $pivot = ViberMessageViberUser::where([
            'viber_message_id' => $message->viber_message_id,
            'viber_user_id' => $this->v_u->viber_user_id

        ])->one();

        if (!$pivot) {
            ViberMessageViberUser::create([
                'viber_message_id' => $message->viber_message_id,
                'viber_user_id' => $this->v_u->viber_user_id,
                $data['event'] . '_at' => $date
            ]);
        } else {
            $key = $data['event'] . '_at';
            if (empty($pivot->$key)) $pivot->update([
                $key => $date
            ]);
        }
        return $this->sendSuccessResponse();
    }
}