<?php

namespace app\module\admin\module\gallery;

class GalleryModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\module\admin\module\gallery\controllers';
    /**
     * @inheritdoc
     */
    public $layout = '@app/module/admin/views/layouts/main';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->defaultRoute = 'album';

        parent::init();
    }
}
