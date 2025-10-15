<?php


namespace app\module\api\module\viber\controllers\handlers;


use Carbon\Carbon;
use Throwable;
use app\module\api\module\viber\controllers\handlers\events\EventHandler;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\interfaces\HandlerInterface;
use app\module\api\module\viber\models\ViberChatViberUser;
use app\module\api\module\viber\models\ViberUser;
use yii\db\StaleObjectException;

class ViberUserHandler extends EventHandler implements HandlerInterface
{

    /**
     * @param $data
     * @return ViberUser
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function run($data)
    {
        if (!empty($data['user'])) {
            Helper::rename_array_key($data['user'], 'id', 'viber_id');
            /** @var ViberUser $v_u */
            $v_u = ViberUser::where(['viber_id' => $data['user']['viber_id']])->one();
            if (!$v_u) {
                $v_u = ViberUser::create($data['user']);
            } else {
                $v_u->update($data['user']);
            }

            $this->updateSubscribeInfo($v_u, $data);

            return $v_u;
        }
        return null;
    }

    /**
     * @param ViberUser $v_u
     * @param $data
     * @throws StaleObjectException
     * @throws Throwable
     */
    private function updateSubscribeInfo(ViberUser $v_u, $data)
    {
        if ($data['event'] == 'subscribed' || $data['event'] == 'message') {
            $attr = [
                'subscribed_at' => empty($data['timestamp']) ? Helper::now()->getTimestamp() :
                    Carbon::createFromTimestampMs($data['timestamp'])->getTimestamp(),
                'unsubscribed_at' => null
            ];
        } else $attr = [];
        if (empty($v_u->viberChats)) {
            $v_u->link('viberChats', $this->chat, $attr);
        } elseif ($data['event'] !== 'conversation_started') {
            /** @var ViberChatViberUser $pivot */
            $pivot = $v_u->getViberChatViberUser()->where([
                'viber_chat_id' => $this->chat->viber_chat_id,
                'viber_user_id' => $v_u->viber_user_id
            ])->one();

            if (!is_null($pivot->unsubscribed_at)) {
                $pivot->update([
                    'unsubscribed_at' => null,
                    'subscribed_at' => Helper::now()->getTimestamp()
                ]);
            }
            if (is_null($pivot->subscribed_at)) {
                $pivot->update(['subscribed_at' => Helper::now()->getTimestamp()]);
            }
        }
    }
}