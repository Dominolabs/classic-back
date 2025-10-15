<?php

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var array */
    public $css = [
        'css/fancybox.css?v=13',
        'css/owl.carousel.min.css?v=13',
        'css/datepicker.min.css?v=13',
        'css/select2.min.css?v=13',
        'css/style.css?v=13',
        'css/additional.css?v=13'
    ];

    /** @var array */
    public $js = [
        'https://cdn.polyfill.io/v2/polyfill.min.js?v=13',
        'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js?v=13',
        'js/fancybox.js?v=13',
        'js/owl.carousel.min.js?v=13',
        'js/datepicker.min.js?v=13',
        'js/datepicker.uk.js?v=13',
        'js/datepicker.en.js?v=13',
        'js/select2.full.min.js?v=13',
        'js/main.js?v=13',
        'js/app.js?v=13',
    ];

    public function init()
    {
        parent::init();

        // Disable standard asset bundles
        Yii::$app->assetManager->bundles = [
            'yii\bootstrap\BootstrapPluginAsset' => [
                'js' => []
            ],
            'yii\bootstrap\BootstrapAsset' => [
                'css' => [],
            ],
            'yii\web\JqueryAsset' => [
                'js'=> []
            ],
        ];
    }
}
