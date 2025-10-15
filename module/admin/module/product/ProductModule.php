<?php

namespace app\module\admin\module\product;

class ProductModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\module\admin\module\product\controllers';
    /**
     * @inheritdoc
     */
    public $layout = '@app/module/admin/views/layouts/main';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->defaultRoute = 'category';

        parent::init();
    }
}
