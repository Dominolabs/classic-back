<?php

use app\module\admin\models\LoginForm;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model LoginForm */

$this->title = 'Авторизация';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>
<div class="login-box">
    <div class="login-logo">
        <a href="<?= Url::to(['/admin']) ?>"><b>Classic</b></a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Введите имя пользователя и пароль</p>
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
            <?= $form->field($model, 'username', $fieldOptions1)->label(false)
                ->textInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('username')]) ?>
            <?= $form->field($model, 'password', $fieldOptions2)->label(false)
                ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
            <?= Alert::widget() ?>
            <div class="row">
                <div class="col-xs-8">
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </div>
                <div class="col-xs-4">
                    <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
        <a href="<?= Url::to(['request-password-reset']) ?>">Забыли пароль?</a><br>
    </div>
</div>
