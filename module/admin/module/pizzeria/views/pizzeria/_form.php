<?php

use app\module\admin\models\Language;
use app\module\admin\module\pizzeria\models\Pizzeria;
use dosamigos\ckeditor\CKEditor;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\pizzeria\models\Pizzeria | app\components\ImageBehavior */
/* @var $descriptions array */
/* @var $languages array */
/* @var $descriptions array */
/* @var $pizzeriaImagesDataProvider yii\data\ArrayDataProvider */
/* @var $placeholder string */
/* @var $errors array */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pizzeria-form box box-primary">
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
                                'id' => 'pizzeria-description-name-' . $key,
                                'name' => 'PizzeriaDescription[' . $key . '][name]',
                            ]) ?>

                            <?= $form->field($description, 'address')->textInput([
                                'id' => 'pizzeria-description-address-' . $key,
                                'name' => 'PizzeriaDescription[' . $key . '][address]',
                            ]) ?>

                            <?= $form->field($description, 'schedule')->textInput([
                                'id' => 'pizzeria-description-schedule-' . $key,
                                'name' => 'PizzeriaDescription[' . $key . '][schedule]',
                            ]) ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else: ?>
            <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
        <?php endif; ?>

        <div class="form-group field-pizzeria-image">

            <?= Html::label($model->getAttributeLabel('image'), 'input-file-image', ['class' => 'control-label', 'style' => 'display: block;']) ?>

            <a href="" id="thumb-media" data-toggle="pizzeria-image" class="img-thumbnail">
                <img src="<?= $model->resizeImage($model->image, 100, 100) ?>" alt="" title="" class="image-thumbnail" data-placeholder="<?= $placeholder ?>" />
            </a>

            <input type="hidden" name="Pizzeria[image]" value="<?= $model->image ?>" id="input-image" />

            <input type="file" accept="image/*" id="input-file-image" class="input-file-image" name="Pizzeria[imageFile]" value="" onchange="onImageChange(this)" style="display: none" />
        </div>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'phones')->textarea(['rows' => 3]) ?>

        <?= $form->field($model, 'instagram')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'gmap')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'status')->dropDownList(Pizzeria::getStatusesList()) ?>

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
<?php $this->registerJs(
    "
    function onImageChange(item) {
        if (!item.value) {
            return;
        }

        var src = window.URL.createObjectURL(item.files[0]);
        
        $(item).closest('div').find('.image-thumbnail').attr('src', src);
    }
    "
, View::POS_END, 'script2'); ?>
