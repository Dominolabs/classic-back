<?php

use app\module\admin\module\currency\models\Currency;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\currency\models\Currency */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'symbol_left')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'symbol_right')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'decimal_place')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'status')->dropDownList(Currency::getStatusesList()) ?>

    </div>
    <div class="box-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
