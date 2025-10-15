<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$identity = Yii::$app->user->identity;

/* @var $this View */
/* @var $content string */
/* @var $identity app\module\admin\models\User  */
?>
<header class="main-header">
    <?= Html::a('<span class="logo-lg"><b>Classic</b></span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li>
                    <a href="<?= Yii::$app->request->hostInfo ?>" target="_blank">
                        <i class="fa fa-share-square-o"></i>
                    </a>
                </li>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= Yii::getAlias('@web'); ?>/image/fa-user-160x160.png" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= $identity->username ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= Yii::getAlias('@web'); ?>/image/fa-user-160x160.png" class="img-circle" alt="User Image"/>
                            <p><?= $identity->username ?></p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= Url::to(['/admin/user/update', 'id' => $identity->user_id]) ?>" class="btn btn-default btn-flat">Профиль</a>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Выход',
                                    Url::to(['/admin/default/logout']),
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
