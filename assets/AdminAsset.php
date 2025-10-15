<?php

namespace app\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var array */
    public $css = [
        'css/site.css',
    ];

    /** @var array */
    public $js = [];

    /** @var array */
    public $depends = [];

    public function init()
    {
        $this->depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

        if (YII_ENV_DEV) {
            $this->js[] = 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js';
        } else {
            $this->js[] = 'https://cdn.jsdelivr.net/npm/vue@2.6.11';
        }

        $this->js[] = 'js/admin/app.js?v=14';
        $this->js[] = 'js/admin/common.js?v=14';

        parent::init();
    }
}
