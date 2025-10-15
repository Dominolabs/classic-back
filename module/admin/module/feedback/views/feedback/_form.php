<?php

use app\module\admin\module\feedback\models\Feedback;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\feedback\models\Feedback */
/* @var $errors array */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="team-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
   <div class="box-body table-responsive">

       <?= $form->field($model, 'name')->textInput()->label('Имя') ?>

       <?= $form->field($model, 'phone')->textInput() ?>

       <?= $form->field($model, 'email')->textInput() ?>

       <?= $form->field($model, 'text')->textarea(['rows' => 3])->label('Текст сообщения') ?>

   </div>
   <div class="box-footer">
       <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
   </div>
    <?php ActiveForm::end(); ?>
</div>

