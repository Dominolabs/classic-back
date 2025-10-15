<?php

namespace app\module\admin\module\currency;

/**
 * Class CurrencyModule.
 *
 * @package app\module\admin
 */
class CurrencyModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\module\admin\module\currency\controllers';
    /**
     * @inheritdoc
     */
    public $layout = '@app/module/admin/views/layouts/main';


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->defaultRoute = 'currency';

        parent::init();
    }
}
