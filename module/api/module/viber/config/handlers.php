<?php


use app\module\admin\models\User;
use app\module\api\module\viber\controllers\handlers\commands\AdminInformationCommandHandler;
use app\module\api\module\viber\controllers\handlers\events\ConversationStartedHandler;
use app\module\api\module\viber\controllers\handlers\events\MessageHandler;
use app\module\api\module\viber\controllers\handlers\events\MessageStatusHandler;
use app\module\api\module\viber\controllers\handlers\events\SubscribedHandler;
use app\module\api\module\viber\controllers\handlers\events\UnsubscribedHandler;
use app\module\api\module\viber\controllers\handlers\tracking_data\AdminInfoHandler;
use app\module\api\module\viber\controllers\handlers\tracking_data\IdentificationHandler;
use app\module\api\module\viber\controllers\handlers\tracking_data\NewConversationHandler;
use app\module\api\module\viber\controllers\handlers\tracking_data\OrderNoHandler;
use app\module\api\module\viber\controllers\handlers\tracking_data\TDMessageHandler;

return [
    'events' => [
        'conversation_started' => ConversationStartedHandler::class,
        'subscribed' => SubscribedHandler::class,
        'unsubscribed' => UnsubscribedHandler::class,
        'delivered' => MessageStatusHandler::class,
        'seen' => MessageStatusHandler::class,
        'message' => MessageHandler::class,
    ],
    'tracking_data' => [
        'new_conversation' => NewConversationHandler::class,
        'identification' => IdentificationHandler::class,
        'message' => TDMessageHandler::class,
        'admin_info' => AdminInfoHandler::class,
        'order_no' => OrderNoHandler::class,
    ],
    'commands' => [
        User::ROLE_ADMIN => [
            'information' => AdminInformationCommandHandler::class
        ]
    ]
];
