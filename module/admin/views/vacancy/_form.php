<?php
/**
 * Create/update form view.
 */

use app\module\admin\models\Language;

use app\module\admin\models\Vacancy;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Vacancy */
/* @var $form yii\widgets\ActiveForm */
/* @var $languages array */
/* @var $descriptions array */
/* @var $dataProviders array */
/* @var $errors array */
//dd(Vacancy::getStatusesList());
?>

<div class="vacancy-form box box-primary">
   <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

   <div class="box-body table-responsive">
       <?php if (!empty($languages)): ?>
          <ul class="nav nav-tabs" id="language">
              <?php foreach ($languages as $language): ?>
                 <li role="presentation"><a href="#language<?= $language['language_id'] ?>" data-toggle="tab"><img src="<?= Language::getImageUrl($language['image'], 16, 16) ?>" title="<?= $language['name'] ?>" /> <?= $language['name'] ?></a></li>
              <?php endforeach; ?>
          </ul>
          <div class="tab-content">
              <?php foreach ($descriptions as $key => $description): ?>
                 <div role="tabpanel" class="tab-pane active" id="language<?= $key ?>">
                    <div class="box-body">
                        <?= $form->field($description, 'name')->textInput([
                            'id' => 'vacancy-description-name-' . $key,
                            'name' => 'VacancyDescription[' . $key . '][name]',
                        ]) ?>
                    </div>
                 </div>
              <?php endforeach ?>
          </div>
       <?php else: ?>
          <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
       <?php endif; ?>

       <?= $form->field($model, 'status')->dropDownList(Vacancy::getStatusesList()) ?>
   </div>

   <div class="box-footer">
       <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
   </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(
    "
    $('#language a:first').tab('show');
    ",
    View::POS_READY,
    'script'
);
?>

