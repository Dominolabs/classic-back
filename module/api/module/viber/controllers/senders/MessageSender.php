<?php


namespace app\module\api\module\viber\controllers\senders;


use app\module\api\module\viber\jobs\ViberSendJob;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\exceptions\ValidationException;
use app\module\api\module\viber\interfaces\SenderInterface;
use app\module\api\module\viber\jobs\ViberBroadcastJob;
use app\module\api\module\viber\models\ViberChat;
use app\module\api\module\viber\models\ViberMessage;
use app\module\api\module\viber\models\ViberUser;
use Yii;

/**
 * Class MessageSender
 * @package viber\controllers\senders
 * @property string $type
 * @property ViberChat $chat
 */
class MessageSender implements SenderInterface
{
    public $chat;
    public $type;

    public function __construct($chat)
    {
        $this->chat = $chat;
    }

    /**
     * @param $to
     * @param $message
     * @param array $additional
     * @throws ValidationException
     * @throws \Throwable
     */
    public function send($to, $message, array $additional = [])
    {
        $t_d =  (array_key_exists('tracking_data', $additional)) ? ['tracking_data' => $additional['tracking_data']] : [];
        $m = ViberMessage::create(array_merge([
            'viber_chat_id' => $this->chat->viber_chat_id,
            'type' => $this->type,
            'message_type' => 'sent',
            'sender' => json_encode(Helper::config('main.sender')),
        ], $t_d, ['message' => json_encode($message)]));

        if (is_array($to)) {
            Yii::$app->queue->push(new ViberBroadcastJob([
                'users' => base64_encode(serialize($to)),
                'm_id' => $m->viber_message_id,
                'chat_id' => $this->chat->viber_chat_id,
                'additional' => base64_encode(serialize($additional)),
            ]));
        } else {
            Yii::$app->queue->push(new ViberSendJob([
                'viber_id' => $to,
                'm_id' => $m->viber_message_id,
                'chat_id' => $this->chat->viber_chat_id,
                'additional' => base64_encode(serialize($additional)),
            ]));
        }
    }
}