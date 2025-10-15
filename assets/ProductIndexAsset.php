<?php

namespace app\assets;

use yii\web\AssetBundle;

class ProductIndexAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var array */
    public $js = [];

    /** @var array */
    public $depends = [];

    public function init()
    {
        $this->depends = [
            AdminAsset::class
        ];

        $this->js[] = 'js/admin/product.js';

        parent::init();
    }
}
