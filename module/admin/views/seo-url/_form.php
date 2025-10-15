<?php
/**
 * Create/update form view.
 */

use app\module\admin\models\Language;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\SeoUrl */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="seo-url-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'language_id')->dropDownList(Language::getList()) ?>

        <?= $form->field($model, 'query')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'keyword')->textInput(['maxlength' => true]) ?>

    </div>
    <div class="box-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
