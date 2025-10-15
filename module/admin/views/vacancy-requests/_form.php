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
/* @var $placeholder string */

?>

<div class="vacancy-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

   <div class="box-body table-responsive">

       <?= $form->field($model, 'vacancy_id')->dropDownList(Vacancy::getListWithNames($model->lang_id))->label('Вакансия')  ?>

       <div class="form-group field-user-avatar">
           <?= Html::label($model->getAttributeLabel('photo'), 'input-file-avatar', ['class' => 'control-label', 'style' => 'display: block;']) ?>

           <div class="img-thumbnail"
                style="width: 200px; height: 200px; display: flex; align-items: center; justify-content: center;">
              <?php if(isset($model->photo)): ?>
               <img src="<?= $model->resizeImage($model->photo, 180, 180) ?>" alt="" title="" class="image-thumbnail"
                    style="max-width: 100%; max-height: 100%;" data-placeholder=""/>
              <?php else: ?>
                 <img src="<?= $model->resizeImage($placeholder, 180, 180) ?>" alt="" title="" class="image-thumbnail"
                      style="max-width: 100%; max-height: 100%;" data-placeholder=""/>
              <?php endif; ?>
           </div>

           <input type="hidden" name="VacancyRequest[photo]" value="<?= $model->photo ?>" id="input-avatar"/>
       </div>

       <?= $form->field($model, 'full_name')->textInput(['maxlength' => true])->label('ФИО') ?>

       <?= $form->field($model, 'age')->textInput(['type' => 'number'])->label('Возраст') ?>

       <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email']) ?>

       <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

       <?= $form->field($model, 'social_links')->textInput(['maxlength' => true])->label('Социальные сети') ?>

       <?= $form->field($model, 'reason')->textarea(['rows' => 3]) ?>

   </div>

   <div class="box-footer">
       <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
   </div>
    <?php ActiveForm::end(); ?>
</div>


