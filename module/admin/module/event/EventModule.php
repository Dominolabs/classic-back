<?php

namespace app\module\admin\module\event;

class EventModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\module\admin\module\event\controllers';
    /**
     * @inheritdoc
     */
    public $layout = '@app/module/admin/views/layouts/main';


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->defaultRoute = 'event';

        parent::init();
    }
}
