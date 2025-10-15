<?php


namespace app\module\api\module\viber\jobs;

use app\module\api\module\viber\controllers\senders\ViberSender;
use app\module\api\module\viber\models\ViberChat;
use app\module\api\module\viber\models\ViberMessage;
use app\module\api\module\viber\models\ViberUser;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class ViberSendJob extends BaseObject implements JobInterface
{

    public $viber_id;
    public $m_id;
    public $chat_id;
    public $additional;

    /**
     * @param Queue $queue
     * @return array|mixed|string|void
     */
    public function execute($queue)
    {

        try {
            $m = ViberMessage::findOne($this->m_id);

            $chat = ViberChat::findOne($this->chat_id);
            $user = ViberUser::findOne(['viber_id' => $this->viber_id]);
            $additional = unserialize(base64_decode($this->additional));

            if ($m && $chat && $user) ViberSender::send($user, $m, $chat, $additional);
        } catch (\Throwable $e) {
        }
    }
}