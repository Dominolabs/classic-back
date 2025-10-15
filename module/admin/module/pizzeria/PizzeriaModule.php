<?php


namespace app\module\admin\module\pizzeria;

class PizzeriaModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->defaultRoute = 'pizzeria';
        $this->controllerNamespace = 'app\module\admin\module\pizzeria\controllers';
        $this->layout = '@app/module/admin/views/layouts/main';

        parent::init();
    }
}
