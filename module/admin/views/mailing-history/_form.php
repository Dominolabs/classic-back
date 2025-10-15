<?php

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\NotificationsHistory */
/* @var $errors array */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="team-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">
        <?= $form->field($model, 'header')->textInput() ?>
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
    </div>
    <div class="box-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
