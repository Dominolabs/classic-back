<?php

use app\module\admin\models\Language;
use app\module\admin\module\event\models\EventCategory;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\event\models\EventCategory */
/* @var $descriptions array */
/* @var $languages array */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="event-category-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
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
                                'id' => 'event-category-description-name-' . $key,
                                'name' => 'EventCategoryDescription[' . $key . '][name]',
                            ]) ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else: ?>
            <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
        <?php endif; ?>

        <?= $form->field($model, 'status')->dropDownList(EventCategory::getStatusesList()) ?>

        <?= $form->field($model, 'sort_order')->textInput() ?>

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

