<?php


namespace app\module\api\module\viber\jobs;

use app\module\api\module\viber\controllers\senders\ViberSender;
use app\module\api\module\viber\models\ViberChat;
use app\module\api\module\viber\models\ViberMessage;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class ViberBroadcastJob extends BaseObject implements JobInterface
{

    public $users;
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
            $this->users = unserialize(base64_decode($this->users));

            $m = ViberMessage::findOne($this->m_id);

            $chat = ViberChat::findOne($this->chat_id);

            $additional = unserialize(base64_decode($this->additional));

            if ($m && $chat) ViberSender::broadcast($this->users, $m, $chat, $additional);
        } catch (\Throwable $e) {
        }
    }
}