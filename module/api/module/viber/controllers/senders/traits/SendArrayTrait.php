<?php

namespace app\module\api\module\viber\controllers\senders\traits;

use app\module\api\module\viber\controllers\helpers\T;
use app\module\api\module\viber\exceptions\ValidationException;

trait SendArrayTrait
{

    public $errors = [];

    /**
     * @param $to
     * @param $message
     * @param array $additional
     * @throws ValidationException
     */
    public function send($to, $message, array $additional = [])
    {
        if (!$this->validateMessage($message)) throw new ValidationException($this->errors);
        $message = [
            'type' => $this->type,
            $this->type => $message,
        ];
        parent::send($to, $message, $additional);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateMessage($data)
    {
        if (!is_array($data)) $this->errors[$this->type][] = T::t('validation', 'This field must be an array.');
        foreach ($data as $key => $datum) {
            if (!in_array($key, $this->keys))
                $this->errors[$this->type . ".$key"][] = T::t('validation', 'This field is not allowed here. ');

            if (!is_string($datum))
                $this->errors[$this->type . ".$key"][] = [T::t('validation', 'This field must be string. ')];
        }

        return empty($this->errors);
    }
}