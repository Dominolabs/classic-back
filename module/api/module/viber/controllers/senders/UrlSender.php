<?php


namespace app\module\api\module\viber\controllers\senders;


use app\module\api\module\viber\exceptions\ValidationException;

class UrlSender extends MessageSender
{

    public $type = 'url';

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
            'media' => $message,
        ];
        parent::send($to, $message, $additional);
    }
}