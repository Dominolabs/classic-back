<?php


namespace app\module\api\module\viber\controllers\senders\traits;


use app\module\api\module\viber\exceptions\ValidationException;

trait SimpleMessageTrait
{
    /**
     * @param $to
     * @param $message
     * @param array $additional
     * @throws ValidationException
     */
    public function send($to, $message, array $additional = [])
    {
        if (!empty($message)) {
            $message = [
                'type' => $this->type,
                $this->type => $message
            ];
            parent::send($to, $message, $additional);
        }
    }
}