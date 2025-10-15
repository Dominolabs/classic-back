<?php

use app\module\admin\models\User;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\User|\app\components\ImageBehavior */
/* @var $form yii\widgets\ActiveForm */
/* @var $placeholder string */
?>
<div class="user-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <div class="form-group field-user-avatar">

            <?= Html::label($model->getAttributeLabel('avatar'), 'input-file-avatar', ['class' => 'control-label', 'style' => 'display: block;']) ?>

            <a href="" id="thumb-media" data-toggle="user-avatar" class="img-thumbnail" style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;">
                <img src="<?= $model->resizeImage($model->avatar, 100, 100) ?>" alt="" title="" class="image-thumbnail" style="max-width: 100%; max-height: 100%;" data-placeholder="<?= $placeholder ?>" />
            </a>

            <input type="hidden" name="User[avatar]" value="<?= $model->avatar ?>" id="input-avatar" />

            <input type="file" accept="image/*" id="input-file-image" class="input-file-image" name="User[avatarFile]" value="" onchange="onImageChange(this)" style="display: none" />
        </div>

        <?= $form->field($model, 'birth_date')->widget(\yii\jui\DatePicker::class, [
            'language' => 'ru',
            'dateFormat' => 'dd.MM.yyyy',
            'options' => [
                'class' => 'form-control',
                'autocomplete' => 'off'
            ]
        ]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'address')->textarea(['maxlength' => true]) ?>

        <?= $form->field($model, 'newPassword')->passwordInput() ?>

        <?= $form->field($model, 'role')->dropDownList(User::getRolesList()) ?>

        <?php if (!$model->isNewRecord): ?>

        <?= $form->field($model, 'promo_code')->textInput(['maxlength' => true, 'readonly' => true]) ?>

        <?php endif; ?>

        <?= $form->field($model, 'device_id')->textInput(['maxlength' => true, 'readonly' => true]) ?>

        <?= $form->field($model, 'ref_promo_code')->textInput(['maxlength' => true]) ?>


        <?= $form->field($model, 'status')->dropDownList(User::getStatusesList()) ?>

    </div>
    <div class="box-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php $this->registerJs("
function onImageChange(item) {
    if (!item.value) {
        return;
    }

    var src = window.URL.createObjectURL(item.files[0]);
    
    $(item).closest('div').find('.image-thumbnail').attr('src', src);
}
", View::POS_END, 'script'); ?>