<?php
/**
 * Create/update form view.
 */

use app\module\admin\models\Language;

use app\module\admin\models\Vacancy;
use app\module\admin\models\VacancyRequest;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\module\admin\models\VacancyRequest */
/* @var $form yii\widgets\ActiveForm */
/* @var $dataProviders array */
/* @var $errors array */

?>

<div class="vacancy-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

   <div class="box-body table-responsive">

       <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Имя') ?>

       <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email']) ?>

   </div>

   <div class="box-footer">
       <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
   </div>
    <?php ActiveForm::end(); ?>
</div>


