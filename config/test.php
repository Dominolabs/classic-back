<?php

use yii\web\UrlNormalizer;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'devseonetcms-tests',
    'basePath' => dirname(__DIR__),  
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],  
    'language' => 'en-US',
    'components' => [
        'db' => $db,
        'mailer' => [
            'useFileTransport' => true,
        ],
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
                'admin' => 'admin/default/index',
                'admin/login' => 'admin/default/login',
                'admin/logout' => 'admin/default/logout',
                'admin/request-password-reset' => 'admin/default/request-password-reset',
                'admin/reset-password' => 'admin/default/reset-password',
                '<alias:\w+>' => 'site/page',
            ],
            'ignoreLanguageUrlPatterns' => [
                '#^admin#' => '#^admin#',
            ],
        ],
        'user' => [
            'identityClass' => 'app\module\admin\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['admin/default/login'],
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-green',
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
    ],
    'params' => $params,
];
