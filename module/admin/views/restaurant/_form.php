<?php

use app\module\admin\models\Banner;
use app\module\admin\models\Language;
use app\module\admin\models\Restaurant;
use app\module\admin\models\RestaurantCategory;
use app\module\admin\module\gallery\models\Album;
use dosamigos\ckeditor\CKEditor;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $restaurant app\module\admin\models\Restaurant */
/* @var $descriptions array */
/* @var $seoUrls array */
/* @var $languages array */
/* @var $placeholder string */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="restaurant-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#main" aria-controls="home" role="tab" data-toggle="tab">Основное</a>
            </li>
            <li role="presentation"><a href="#data" aria-controls="profile" role="tab" data-toggle="tab">Данные</a></li>
            <li role="presentation"><a href="#social" aria-controls="social" role="tab" data-toggle="tab">Социальные
                    сети</a></li>
            <li role="presentation"><a href="#seo" aria-controls="messages" role="tab" data-toggle="tab">SEO</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="main">
                <div class="box-body">
                    <?= $form->field($restaurant, 'online_delivery')->checkbox() ?>

                    <?= $form->field($restaurant, 'online_delivery_orders_processing')->checkbox() ?>

                    <?= $form->field($restaurant, 'self_picking')->checkbox() ?>

                    <?php if (!empty($languages)): ?>
                        <ul class="nav nav-tabs" id="language">
                            <?php foreach ($languages as $language): ?>
                                <li role="presentation"><a href="#language<?= $language['language_id'] ?>"
                                                           data-toggle="tab"><img
                                                src="<?= Language::getImageUrl($language['image'], 16, 16) ?>"
                                                title="<?= $language['name'] ?>"/> <?= $language['name'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tab-content">
                            <?php foreach ($descriptions as $key => $description): ?>
                                <div role="tabpanel" class="tab-pane active" id="language<?= $key ?>">
                                    <div class="box-body">
                                        <?= $form->field($description, 'title')->textInput([
                                            'id' => 'restaurant-description-title-' . $key,
                                            'name' => 'RestaurantDescription[' . $key . '][title]',
                                        ]) ?>

                                        <?= $form->field($description, 'description1')->widget(CKEditor::class, [
                                            'options' => [
                                                'id' => 'restaurant-description-description1-' . $key,
                                                'name' => 'RestaurantDescription[' . $key . '][description1]',
                                            ],
                                            'clientOptions' => [
                                                'allowedContent' => true,
                                                'fillEmptyBlocks' => false,
                                                'autoParagraph' => false,
                                                'extraPlugins' => 'uploadimage',
                                                'filebrowserUploadUrl' => Url::to(['/admin/default/upload'])
                                            ],
                                            'preset' => 'custom',
                                        ]) ?>

                                        <?= $form->field($description, 'description2')->widget(CKEditor::class, [
                                            'options' => [
                                                'id' => 'restaurant-description-description2-' . $key,
                                                'name' => 'RestaurantDescription[' . $key . '][description2]',
                                            ],
                                            'clientOptions' => [
                                                'allowedContent' => true,
                                                'fillEmptyBlocks' => false,
                                                'autoParagraph' => false,
                                                'extraPlugins' => 'uploadimage',
                                                'filebrowserUploadUrl' => Url::to(['/admin/default/upload'])
                                            ],
                                            'preset' => 'custom',
                                        ]) ?>

                                        <?= $form->field($description, 'address')->textInput([
                                            'id' => 'restaurant-description-address-' . $key,
                                            'name' => 'RestaurantDescription[' . $key . '][address]',
                                        ]) ?>

                                        <?= $form->field($description, 'phone')->textInput([
                                            'id' => 'restaurant-description-phone-' . $key,
                                            'name' => 'RestaurantDescription[' . $key . '][phone]',
                                        ]) ?>

                                        <?= $form->field($description, 'schedule')->textarea([
                                            'id' => 'restaurant-description-schedule-' . $key,
                                            'name' => 'RestaurantDescription[' . $key . '][schedule]',
                                            'rows' => 5,
                                        ]) ?>

                                        <?= $form->field($description, 'gmap')->textInput([
                                            'id' => 'restaurant-description-gmap-' . $key,
                                            'name' => 'RestaurantDescription[' . $key . '][gmap]',
                                        ]) ?>


                                        <?= $form->field($description, 'meta_title')->textInput([
                                            'id' => 'restaurant-description-meta_title-' . $key,
                                            'name' => 'RestaurantDescription[' . $key . '][meta_title]',
                                        ]) ?>

                                        <?= $form->field($description, 'meta_description')->textInput([
                                            'id' => 'restaurant-description-meta_description-' . $key,
                                            'name' => 'RestaurantDescription[' . $key . '][meta_description]'
                                        ]) ?>

                                        <?= $form->field($description, 'meta_keyword')->textInput([
                                            'id' => 'restaurant-description-meta_keyword-' . $key,
                                            'name' => 'RestaurantDescription[' . $key . '][meta_keyword]',
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endforeach ?>

                            <?= $form->field($restaurant, 'lat')->textInput()->label('Широта') ?>

                            <?= $form->field($restaurant, 'long')->textInput()->label('Долгота') ?>
                        </div>
                    <?php else: ?>
                        <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или
                            более языков!</p>
                    <?php endif; ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="data">
                <div class="box-body">

                    <?= $form->field($restaurant, 'restaurant_category_id')->dropDownList(ArrayHelper::merge([''], RestaurantCategory::getList())) ?>

                    <div class="form-group field-restaurant-image">

                        <?= Html::label($restaurant->getAttributeLabel('image'), 'input-file-image-1', ['class' => 'control-label', 'style' => 'display: block;']) ?>

                        <a href="" id="thumb-image" data-toggle="restaurant-image" class="img-thumbnail"
                           style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;"
                           data-row-id="1">
                            <img src="<?= $restaurant->getBehavior('image')->resizeImage($restaurant->image, 100, 100) ?>"
                                 alt="" title="" class="image-thumbnail" style="max-width: 100%; max-height: 100%;"
                                 data-placeholder="<?= $placeholder ?>"/>
                        </a>
                        <input type="hidden" name="Restaurant[image]" value="<?= $restaurant->image ?>"
                               id="input-image"/>

                        <input type="file" accept="image/*" id="input-file-image-1" class="input-file-imag-1"
                               name="Restaurant[imageFile]" value="" onchange="onImageChange(this)"
                               style="display: none"/>
                    </div>

                    <div class="form-group field-restaurant-image-transparent">

                        <?= Html::label($restaurant->getAttributeLabel('image_transparent'), 'input-file-image-3', ['class' => 'control-label', 'style' => 'display: block;']) ?>

                        <a href="" id="thumb-image" data-toggle="restaurant-image" class="img-thumbnail"
                           style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;"
                           data-row-id="3">
                            <img src="<?= $restaurant->getBehavior('imageTransparent')->resizeImage($restaurant->image_transparent, 100, 100) ?>"
                                 alt="" title="" class="image-thumbnail" style="max-width: 100%; max-height: 100%;"
                                 data-placeholder="<?= $placeholder ?>"/>
                        </a>
                        <input type="hidden" name="Restaurant[image_transparent]"
                               value="<?= $restaurant->image_transparent ?>" id="input-image-transparent"/>

                        <input type="file" accept="image/*" id="input-file-image-3" class="input-file-image-3"
                               name="Restaurant[imageTransparentFile]" value="" onchange="onImageChange(this)"
                               style="display: none"/>
                    </div>

                    <div class="form-group field-restaurant-background-image">

                        <?= Html::label($restaurant->getAttributeLabel('background_image'), 'input-file-image-2', ['class' => 'control-label', 'style' => 'display: block;']) ?>

                        <a href="" id="thumb-image" data-toggle="restaurant-image" class="img-thumbnail"
                           style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;"
                           data-row-id="2">
                            <img src="<?= $restaurant->getBehavior('backgroundImage')->resizeImage($restaurant->background_image, 100, 100) ?>"
                                 alt="" title="" class="image-thumbnail" style="max-width: 100%; max-height: 100%;"
                                 data-placeholder="<?= $placeholder ?>"/>
                        </a>
                        <input type="hidden" name="Restaurant[background_image]"
                               value="<?= $restaurant->background_image ?>" id="input-background-image"/>

                        <input type="file" accept="image/*" id="input-file-image-2" class="input-file-image-2"
                               name="Restaurant[backgroundImageFile]" value="" onchange="onImageChange(this)"
                               style="display: none"/>
                    </div>

                    <?= $form->field($restaurant, 'top_banner_id')->dropDownList(Banner::getList()) ?>

                    <?= $form->field($restaurant, 'gallery_id')->dropDownList(Album::getList()) ?>

                    <?= $form->field($restaurant, 'menu_banner_id')->dropDownList(Banner::getList()) ?>

                    <?= $form->field($restaurant, 'status')->dropDownList(Restaurant::getStatusesList()) ?>

                    <?= $form->field($restaurant, 'sort_order')->textInput() ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="social">
                <div class="box-body">

                    <?= $form->field($restaurant, 'facebook')->textInput() ?>

                    <?= $form->field($restaurant, 'instagram')->textInput() ?>

                    <?= $form->field($restaurant, 'youtube')->textInput() ?>

                    <?= $form->field($restaurant, 'vk')->textInput() ?>

                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="seo">
                <div class="box-body">

                    <div class="alert alert-info seo-url-alert"><i class="fa fa-info-circle"></i> SEO URL должен быть
                        уникальным на всю систему и не содержать пробелов.
                    </div>

                    <?php if (!empty($languages)): ?>
                        <ul class="nav nav-tabs" id="language-seo">
                            <?php foreach ($languages as $language): ?>
                                <li role="presentation"><a href="#language-seo<?= $language['language_id'] ?>"
                                                           data-toggle="tab"><img
                                                src="<?= Language::getImageUrl($language['image'], 16, 16) ?>"
                                                title="<?= $language['name'] ?>"/> <?= $language['name'] ?></a></li>
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
                        <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или
                            более языков!</p>
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
<?php
$this->registerJs(
    "
    function onImageChange(item) {
        if (!item.value) {
            return;
        }

        var src = window.URL.createObjectURL(item.files[0]);
        
        $(item).closest('div').find('.image-thumbnail').attr('src', src);
    }
    ",
    View::POS_END,
    'script2'
);
?>
