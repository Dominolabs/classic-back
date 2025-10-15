<?php
/**
 * Create/update form view.
 */

use app\module\admin\models\Language;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Language| app\components\ImageBehavior */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="language-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'imageFile')->widget(FileInput::class, [
            'options' => [
                'accept' => 'image/*',
                'multiple' => false,
            ],
            'pluginOptions' => [
                'showRemove' => false,
                'showUpload' => false,
                'showCaption' => false,
                'initialPreview' => [
                    $model->resizeImage($model->image, 320, 320)
                ],
                'initialPreviewAsData' => true,
                'overwriteInitial' => true,
            ]
        ]); ?>

        <?= $form->field($model, 'status')->dropDownList(Language::getStatusesList()) ?>

        <?= $form->field($model, 'sort_order')->textInput() ?>

    </div>
    <div class="box-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
