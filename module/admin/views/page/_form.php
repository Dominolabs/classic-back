<?php

use app\module\admin\models\Banner;
use app\module\admin\models\Language;
use app\module\admin\models\Page;
use app\module\admin\module\gallery\models\Album;
use dosamigos\ckeditor\CKEditor;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $page app\module\admin\models\Page */
/* @var $descriptions array */
/* @var $seoUrls array */
/* @var $languages array */
/* @var $placeholder string */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="page-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#main" aria-controls="home" role="tab" data-toggle="tab">Основное</a></li>
            <li role="presentation"><a href="#data" aria-controls="profile" role="tab" data-toggle="tab">Данные</a></li>
            <li role="presentation"><a href="#social" aria-controls="profile" role="tab" data-toggle="tab">Социальные сети</a></li>
            <li role="presentation"><a href="#seo" aria-controls="messages" role="tab" data-toggle="tab">SEO</a></li>
            <li role="presentation"><a href="#footer" aria-controls="messages" role="tab" data-toggle="tab">Адреса в подвале сайта (footer)</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="main">
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
                                <?= $form->field($description, 'title')->textInput([
                                    'id' => 'page-description-title-' . $key,
                                    'name' => 'PageDescription[' . $key . '][title]',
                                ]) ?>

                                <?= $form->field($description, 'description1')->widget(CKEditor::class, [
                                    'options' => [
                                        'id' => 'restaurant-description-description1-' . $key,
                                        'name' => 'PageDescription[' . $key . '][description1]',
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
                                        'name' => 'PageDescription[' . $key . '][description2]',
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

                                <?= $form->field($description, 'meta_title')->textInput([
                                    'id' => 'page-description-meta_title-' . $key,
                                    'name' => 'PageDescription[' . $key . '][meta_title]',
                                ]) ?>

                                <?= $form->field($description, 'meta_description')->textInput([
                                    'id' => 'page-description-meta_description-' . $key,
                                    'name' => 'PageDescription[' . $key . '][meta_description]'
                                ]) ?>

                                <?= $form->field($description, 'meta_keyword')->textInput([
                                    'id' => 'page-description-meta_keyword-' . $key,
                                    'name' => 'PageDescription[' . $key . '][meta_keyword]',
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
            <div role="tabpanel" class="tab-pane" id="data">
                <div class="box-body">
                    <div class="form-group field-page-image">

                        <?= Html::label($page->getAttributeLabel('image'), 'input-file-image', ['class' => 'control-label', 'style' => 'display: block;']) ?>

                        <a href="" id="thumb-image" data-toggle="page-image" class="img-thumbnail" style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= $page->resizeImage($page->image, 100, 100) ?>" alt="" title="" class="image-thumbnail" style="max-width: 100%; max-height: 100%;" data-placeholder="<?= $placeholder ?>" />
                        </a>
                        <input type="hidden" name="Page[image]" value="<?= $page->image ?>" id="input-image" />

                        <input type="file" accept="image/*" id="input-file-image" class="input-file-image" name="Page[imageFile]" value="" onchange="onImageChange(this)" style="display: none" />
                    </div>

                    <?= $form->field($page, 'top_banner_id')->dropDownList(Banner::getList(), ['prompt' => 'Выбирите из списка...']) ?>

                    <?= $form->field($page, 'gallery_id')->dropDownList(Album::getList(), ['prompt' => 'Выбирите из списка...']) ?>

                    <?= $form->field($page, 'status')->dropDownList(Page::getStatusesList()) ?>

                    <?= $form->field($page, 'sort_order')->textInput() ?>
                </div>
            </div>
           <div role="tabpanel" class="tab-pane" id="social">
              <div class="box-body">

                  <?= $form->field($page, 'facebook')->textInput() ?>

                  <?= $form->field($page, 'instagram')->textInput() ?>

                  <?= $form->field($page, 'youtube')->textInput() ?>

                  <?= $form->field($page, 'vk')->textInput() ?>

              </div>
           </div>
            <div role="tabpanel" class="tab-pane" id="seo">
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
           <div role="tabpanel" class="tab-pane" id="footer">
              <div class="box-body">
                 <div class="alert alert-info seo-url-alert"><i class="fa fa-info-circle"></i> Добавление адресов, которые отображаются в подвале сайта</div>

                  <?php $footer_columns = isset($page->footer_columns) ? json_decode($page->footer_columns, true) : []; ?>
                  <?php
                  $languagesData = [];
                  foreach ($languages as $language) {
                      $language['icon'] = Language::getImageUrl($language['image'], 16, 16);
                      $languagesData[] = $language;
                  }
                  ?>
                  <page-footer-addresses-component
                      :footer_columns="<?= htmlspecialchars(json_encode($footer_columns)) ?>"
                      :languages="<?= htmlspecialchars(json_encode($languagesData)) ?>"
                      :model_name="'Page'"
                  ></page-footer-addresses-component>
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
