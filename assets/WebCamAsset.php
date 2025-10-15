<?php

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class WebCamAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/fancybox.css?v=13',
        'css/owl.carousel.min.css?v=13',
        'css/datepicker.min.css?v=13',
        'css/style.css?v=13',
    ];
    public $js = [
        'https://cdn.polyfill.io/v2/polyfill.min.js?v=13',
        'js/fancybox.js?v=13',
        'js/owl.carousel.min.js?v=13',
        'js/datepicker.min.js?v=13',
        'js/datepicker.uk.js?v=13',
        'js/datepicker.en.js?v=13',
        'js/main.js?v=13',
    ];

    /**
     * @inheritdoc
     */
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
        ];
    }
}
