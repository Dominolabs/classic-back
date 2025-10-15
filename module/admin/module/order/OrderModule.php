<?php

namespace app\module\admin\module\order;

class OrderModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\module\admin\module\order\controllers';
    /**
     * @inheritdoc
     */
    public $layout = '@app/module/admin/views/layouts/main';


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->defaultRoute = 'order';

        parent::init();
    }
}
