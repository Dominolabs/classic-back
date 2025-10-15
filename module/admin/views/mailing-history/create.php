<?php

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\MailingForm */

$this->title = 'Отправка Email';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-send">
    <div class="email-form box box-primary">
        <?php $form = ActiveForm::begin(); ?>
        <div class="box-body table-responsive">
            <div class="alert alert-info seo-url-alert"><i class="fa fa-info-circle"></i> Внимание! Сообщение
                отправляется всем пользователям мобильного приложения.
            </div>
            <?= $form->field($model, 'header')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'message')->widget(CKEditor::class, [
                'clientOptions' => [
                    'allowedContent' => true,
                    'fillEmptyBlocks' => false,
                    'autoParagraph' => false,
                    'extraPlugins' => 'uploadimage',
                    'filebrowserUploadUrl' => Url::to(['/admin/default/upload'])
                ],
                'preset' => 'custom',
            ]) ?>

            <div class="form-group">
                <?= Html::checkbox("test", false, [
                    'label' => 'Тестовая рассылка',
                ]) ?>
            </div>


            <div class="form-group">

                <?= Html::label('Email адреса для тестовой рассылки', 'test_emails') ?>

                <?= Html::textInput("test_emails", "devseonet@gmail.com", [
                    'class' => 'form-control'
                ]) ?>
            </div>
        </div>
        <div class="box-footer">
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-success btn-flat']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
