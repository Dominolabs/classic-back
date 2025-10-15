<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model PasswordResetRequestForm */

use app\module\admin\models\PasswordResetRequestForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = 'Запрос на зброс пароля';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-box">
    <div class="login-logo">
        <a href="<?= Url::to(['/admin']) ?>"><b>Classic</b></a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Введите ваш email. Вам будет отправлена ссылка на сброс пароля.</p>
        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
            <?= $form->field($model, 'email')->label(false)->textInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('email')]) ?>
            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>