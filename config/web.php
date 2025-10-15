<?php

use yii\queue\file\Queue;
use yii\queue\LogBehavior;
use yii\web\UrlNormalizer;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'classic',
    'name' => 'Classic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log',
        'queue',
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\module\admin\AdminModule',
        ],
        'api' => [
            'class' => 'app\module\api\ApiModule',
        ],
        'sitemap' => [
            'class' => 'app\module\sitemap\SitemapModule',
            'models' => [
                'app\module\admin\models\Page',
            ],
            'urls' => [
                [
                    'loc' => 'menu',
                    'changefreq' => 'daily',
                    'priority' => 0.8,
                ],
            ],
            'cacheExpire' => 1, // 1 sec
        ],
        'redactor' => [
            'class' => 'yii\redactor\RedactorModule',
            'uploadDir' => '@webroot/image/redactor',
            'uploadUrl' => '@web/image/redactor',
            'imageAllowExtensions' => ['jpg', 'png', 'gif']
        ],
        'gridview' => [
            'class' => 'kartik\grid\Module'
        ],
    ],
    'components' => [
        'queue' => [
            'class' => Queue::class,
            'path' => '@runtime/queue',
            'as log' => LogBehavior::class,
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'timeZone' => 'Europe/Kyiv',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'X_4ym1HBvGB--jdPjIIyOGz1I6pulYWT',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\module\admin\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['admin/default/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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
                'username' => 'dominoit.agency@gmail.com',
                'password' => 'fvbqdszjuynzyldw',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['api'],
                    'logFile' => '@runtime/logs/api.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['info', 'error', 'warning'],
                    'categories' => ['notification'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/notification.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['liqpay'],
                    'logFile' => '@runtime/logs/liqpay.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['booking-mails'],
                    'logFile' => '@runtime/logs/booking_mails.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['db'],
                    'logFile' => '@runtime/logs/db.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['printer'],
                    'logFile' => '@runtime/logs/printer.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['feedback'],
                    'logFile' => '@runtime/logs/feedback.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['images'],
                    'logFile' => '@runtime/logs/images.log'
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
                    'categories' => ['product'],
                    'logFile' => '@runtime/logs/product.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['emails'],
                    'logFile' => '@runtime/logs/emails.log'
                ],
            ]
        ],
        'db' => $db,
        'urlManager' => [
            'class' => 'app\components\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'action' => UrlNormalizer::ACTION_REDIRECT_PERMANENT,
            ],
            'rules' => [
                '/' => 'site/index',
                'do' => 'test/do',
                'unsubscribe' => 'user/unsubscribe',
                'fix' => 'user/fix',
                'viber' => 'api/viber/viber/viber',
                'viber/send' => 'api/viber/viber/send',
                'viber/broadcast' => 'api/viber/viber/broadcast',
                'api/viber/seeder/create-command' => 'api/viber/seeder/create-command',
                'api/order/payment' => 'order/payment',
                'api/order/processing' => 'order/processing',
                'api/<controller:[\w\-]+>/<action:[\w\-]+>' => 'api/<controller>/<action>',
                'admin' => 'admin/default/index',
                'admin/login' => 'admin/default/login',
                'admin/logout' => 'admin/default/logout',
                'admin/request-password-reset' => 'admin/default/request-password-reset',
                'admin/reset-password' => 'admin/default/reset-password',
                'admin/<controller:[\w\-]+>' => 'admin/<controller>/index',
                'admin/<controller:[\w\-]+>/<action:[\w\-]+>' => 'admin/<controller>/<action>',
                'admin/<module:[\w\-]+>/<controller:[\w\-]+>' => 'admin/<module>/<controller>/index',
                'admin/<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>' => 'admin/<module>/<controller>/<action>',
                [
                    'pattern' => 'sitemap',
                    'route' => 'sitemap/default/index',
                    'suffix' => '.xml'
                ],
                'app' => 'site/app',
                'site/reviews' => 'site/reviews',
                'app/<alias:[a-zA-Z0-9_-]+>' => 'site/app',
                'menu' => 'site/menu',
                'webcam' => 'site/webcam',
                'booking' => 'booking/index',
                'booking/add-booking-to-cart' => 'booking/add-booking-to-cart',
                'booking/clear-booking-cart' => 'booking/clear-booking-cart',
                'booking/order' => 'booking/ordering-page',
                'booking/order/create' => 'booking/create-booking-order',
                'booking/<alias:[a-zA-Z0-9_-]+>' => 'booking/show',
                '<alias:[a-zA-Z0-9_-]+>' => 'site/page',
                '<alias:[a-zA-Z0-9_-]+>/gallery' => 'site/gallery',
                '<alias:[\w_\/-]+>' => 'site/page',
            ],
            'ignoreLanguageUrlPatterns' => [
                '#^admin#' => '#^admin#',
            ],
            'enableLanguageDetection' => false,
            'enableLanguagePersistence' => false,
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-red',
                ],
            ],
        ],
        'view' => [
            'theme' => [
                'basePath' => '@app/themes/default',
                'baseUrl' => '@web/themes/default',
                'pathMap' => [
                    '@app/views' => '@app/themes/default',
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'app\components\AppMessageSource',
                    'enableCaching' => true,
                    'cachingDuration' => 0,
                    'sourceLanguage' => 'ru-RU',
                ],
            ],
        ],
        'cart' => [
            'class' => 'app\components\cart\ShoppingCart',
            'cartId' => 'cart',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'adminlte' => '@vendor/dmstr/yii2-adminlte-asset/gii/templates/crud/simple',
                ]
            ]
        ],
    ];
}

return $config;
