<?php


namespace app\module\api\module\viber\controllers\senders;


use app\module\api\module\viber\exceptions\ValidationException;

class StickerSender extends MessageSender
{

    public $type = 'sticker';

    /**
     * @param $to
     * @param $message
     * @param array $additional
     * @throws ValidationException
     */
    public function send($to, $message, array $additional = [])
    {
        $message = [
            'type' => $this->type,
            'sticker_id' => $message,
        ];
        parent::send($to, $message, $additional);
    }
}