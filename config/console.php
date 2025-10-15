<?php

use yii\queue\file\Queue;
use yii\queue\LogBehavior;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'queue',
        ],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'devseonet.test@gmail.com',
                'password' => 'indd urxu upkj ilen',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
        'queue' => [
            'class' => Queue::class,
            'path' => '@runtime/queue',
            'as log' => LogBehavior::class,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['rabbitmq'],
                    'logFile' => '@runtime/logs/rabbitmq.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['info'],
                    'categories' => ['printer'],
                    'logFile' => '@runtime/logs/printer.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['viber'],
                    'logFile' => '@runtime/logs/viber.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['emails'],
                    'logFile' => '@runtime/logs/emails.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['images'],
                    'logFile' => '@runtime/logs/images.log'
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
