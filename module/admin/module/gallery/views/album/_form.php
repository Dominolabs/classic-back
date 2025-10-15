<?php

use app\module\admin\module\gallery\models\AlbumImage;
use app\module\admin\models\Language;
use app\module\admin\module\gallery\models\Album;
use app\module\admin\module\gallery\models\AlbumCategory;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\gallery\models\Album | app\components\ImageBehavior */
/* @var $descriptions array */
/* @var $languages array */
/* @var $descriptions array */
/* @var $albumImagesDataProvider yii\data\ArrayDataProvider */
/* @var $placeholder string */
/* @var $errors array */
/* @var $seoUrls array */
/* @var $initialPreview array */
/* @var $initialPreviewConfig array */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="album-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tab-main" aria-controls="tab-main" role="tab" data-toggle="tab">Основное</a></li>
            <li role="presentation"><a href="#tab-images" aria-controls="tab-images" role="tab" data-toggle="tab">Изображения</a></li>
            <li role="presentation"><a href="#tab-seo" aria-controls="tab-seo" role="tab" data-toggle="tab">SEO</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="tab-main">
                <div class="box-body">
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
                                            'id' => 'album-description-name-' . $key,
                                            'name' => 'AlbumDescription[' . $key . '][name]',
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
                    <?php endif; ?>

                    <div class="form-group field-album-main-image">

                        <?= Html::label($model->getAttributeLabel('image'), 'input-file-image', ['class' => 'control-label', 'style' => 'display: block;']) ?>

                        <a href="" id="thumb-media" data-toggle="album-main-image" class="img-thumbnail" style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= $model::getImageUrl($model->image, 100, 100) ?>" alt="" title="" class="image-thumbnail" style="max-width: 100%; max-height: 100%;" data-placeholder="<?= $placeholder ?>" />
                        </a>
                        <input type="hidden" name="Album[image]" value="<?= $model->image ?>" id="input-image" />

                        <input type="file" accept="image/*" id="input-file-image" class="input-file-image" name="Album[imageFile]" value="" onchange="onMainImageChange(this)" style="display: none" />
                    </div>

                    <?= $form->field($model, 'status')->dropDownList(Album::getStatusesList()) ?>

                    <?= $form->field($model, 'sort_order')->textInput() ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab-images">
                <div class="box-body">
                    <?= FileInput::widget([
                        'model' => new AlbumImage(),
                        'attribute' => 'imageFile[]',
                        'name' => 'imageFile[]',
                        'language' => 'ru',
                        'options'=>[
                            'id' => 'file-input',
                            'multiple' => true,
                            'accept' => 'image/*'
                        ],
                        'pluginOptions' => [
                            'uploadUrl' => Url::to(['upload-image']),
                            'uploadAsync' => true,
                            'showUpload' => false,
                            'showRemove' => false,
                            'showClose' => false,
                            'initialPreviewAsData' => true,
                            'overwriteInitial' => false,
                            'allowedFileExtensions' => ['jpg', 'jpeg', 'gif', 'png'],
                            'maxFileSize' => 10 * 1024,  // 10 Mb
                            'maxFileCount' => 0,
                            'initialPreview' => $initialPreview,
                            'initialPreviewConfig' => $initialPreviewConfig
                        ]
                    ]); ?>
                    <?= $form->field($model, 'images')->hiddenInput(['id' => 'file-input-images'])->label(false) ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab-seo">
                <div class="box-body">

                    <div class="alert alert-info seo-url-alert"><i class="fa fa-info-circle"></i> SEO URL должен быть уникальным на всю систему и не содержать пробелов.</div>

                    <?php if (!empty($languages)): ?>
                        <ul class="nav nav-tabs" id="language-seo">
                            <?php foreach ($languages as $language): ?>
                                <li role="presentation"><a href="#language-seo<?= $language['language_id'] ?>" data-toggle="tab"><img src="<?= Language::getImageUrl($language['image'], 16, 16) ?>" title="<?= $language['name'] ?>" /> <?= $language['name'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tab-content">
                            <?php foreach ($seoUrls as $key => $seoUrl): ?>
                                <div role="tabpanel" class="tab-pane active" id="language-seo<?= $key ?>">
                                    <div class="box-body">
                                        <?= $form->field($seoUrl, 'keyword')->textInput([
                                            'id' => 'seourl-keyword-' . $key,
                                            'name' => 'SeoUrl[' . $key . '][keyword]',
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
    $('#language-seo a:first').tab('show');
    ",
    View::POS_READY,
    'script'
);
?>
<?php $this->registerJs(
    <<<"SCRIPT"
    $('#language a:first').tab('show');

    function onMainImageChange(item) {
        if (!item.value) {
            return;
        }

        var src = window.URL.createObjectURL(item.files[0]);
        
        $(item).closest('div').find('.image-thumbnail').attr('src', src);
    }
SCRIPT
, View::POS_END, 'script2'); ?>
