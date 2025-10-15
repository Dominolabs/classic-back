<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model ResetPasswordForm */

use app\module\admin\models\ResetPasswordForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = 'Сброс пароля';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-box">
    <div class="login-logo">
        <a href="<?= Url::to(['/admin']) ?>"><b>Classic</b></a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Введите новый пароль</p>
        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
        <?= $form->field($model, 'password')->label(false)->passwordInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('password')]) ?>
        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>