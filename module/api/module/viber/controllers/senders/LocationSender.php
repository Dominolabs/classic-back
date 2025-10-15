<?php


namespace app\module\api\module\viber\controllers\senders;


use app\module\api\module\viber\controllers\senders\traits\SendArrayTrait;
use app\module\api\module\viber\exceptions\ValidationException;

class LocationSender extends MessageSender
{
    use SendArrayTrait;

    public $keys = ['lat', 'lon'];

    public $type = 'location';
}