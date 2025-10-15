<?php

namespace app\module\api;

use Yii;

class ApiModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\module\api\controllers';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->modules = [
            'viber' => [
                'class' => 'app\module\api\module\viber\ViberModule',
            ],
        ];
        Yii::$app->user->enableSession = false;
    }
}
