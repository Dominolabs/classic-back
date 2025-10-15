<?php


namespace app\module\api\module\viber\controllers\handlers\events;


use app\module\api\module\viber\controllers\helpers\Helper;
use Throwable;
use app\module\api\module\viber\interfaces\HandlerInterface;
use app\module\api\module\viber\models\ViberUser;
use yii\db\StaleObjectException;

class UnsubscribedHandler extends EventHandler implements HandlerInterface
{
    /**
     * @param $data
     * @return array|void|null
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function run($data)
    {
        if (!empty($data['user_id'])) $this->v_u = ViberUser::where(['viber_id' => $data['user_id']])->one();
        if (!$this->v_u || !$this->chat) return;

        $pivot = $this->v_u->getViberChatViberUser()->where([
            'viber_chat_id' => $this->chat->viber_chat_id,
            'viber_user_id' => $this->v_u->viber_user_id
        ])->one();
        if ($pivot) {
            $pivot->update([
                'unsubscribed_at' => Helper::now()->getTimestamp()
            ]);
        }
        return $this->sendSuccessResponse();
    }
}