<?php

namespace app\module\admin\module\feedback;

class FeedbackModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\module\admin\module\feedback\controllers';
    /**
     * @inheritdoc
     */
    public $layout = '@app/module/admin/views/layouts/main';


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->defaultRoute = 'feedback';

        parent::init();
    }
}
