<?php


namespace app\module\api\module\viber\controllers\senders;


use app\module\api\module\viber\controllers\senders\traits\SendArrayTrait;
use app\module\api\module\viber\exceptions\ValidationException;

class ContactSender extends MessageSender
{
    use SendArrayTrait;

    public $keys = ['name', 'phone_number'];

    public $type = 'contact';
}