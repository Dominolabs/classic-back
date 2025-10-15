<?php

use app\module\api\module\viber\controllers\senders\ContactSender;
use app\module\api\module\viber\controllers\senders\FileSender;
use app\module\api\module\viber\controllers\senders\LocationSender;
use app\module\api\module\viber\controllers\senders\PictureSender;
use app\module\api\module\viber\controllers\senders\RichMediaSender;
use app\module\api\module\viber\controllers\senders\StickerSender;
use app\module\api\module\viber\controllers\senders\TextSender;
use app\module\api\module\viber\controllers\senders\UrlSender;

return [
    'text' => TextSender::class,
    'picture' => PictureSender::class,
//    'video' => Sender::class,
    'file' => FileSender::class,
    'location' => LocationSender::class,
    'contact' => ContactSender::class,
    'sticker' => StickerSender::class,
    'url' => UrlSender::class,
    'rich_media' => RichMediaSender::class
];
