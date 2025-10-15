<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\NotificationForm */

$this->title = 'Отправка PUSH-уведомления';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-send">
    <div class="notification-form box box-primary">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body table-responsive">

            <div class="alert alert-info seo-url-alert"><i class="fa fa-info-circle"></i> Внимание! Уведомление отправляется всем пользователям мобильного приложения.</div>

            <?= $form->field($model, 'header')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'message')->textarea(['maxlength' => true]) ?>
        </div>
        <div class="box-footer">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-success btn-flat']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
