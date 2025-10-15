<?php

namespace app\module\api\module\viber\interfaces;

interface SenderInterface
{
    public function send($to, $message, array $additional = []);
}