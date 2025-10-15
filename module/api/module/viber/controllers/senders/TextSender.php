<?php


namespace app\module\api\module\viber\controllers\senders;


use app\module\api\module\viber\controllers\senders\traits\SimpleMessageTrait;

class TextSender extends MessageSender
{
    use SimpleMessageTrait;

    public $type = 'text';
}