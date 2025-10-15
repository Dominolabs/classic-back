<?php


namespace app\module\api\module\viber\controllers\senders;


use app\module\api\module\viber\controllers\senders\traits\SimpleMessageTrait;
use app\module\api\module\viber\exceptions\ValidationException;

class RichMediaSender extends MessageSender
{
    use SimpleMessageTrait;

    public $type = 'rich_media';
}