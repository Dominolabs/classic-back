<?php

use app\components\ImageBehavior;
use app\module\admin\models\Language;
use app\module\admin\models\Classic;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $product Classic | ImageBehavior */
/* @var $descriptions array */
/* @var $languages array */
/* @var $placeholder string */
/* @var $form yii\widgets\ActiveForm */
/* @var $seoUrls array */

$pizzaCategoryId = (int)Yii::$app->params['pizzaCategoryId'] ?: 0;
$noodlesCategoryId = (int)Yii::$app->params['noodlesCategoryId'] ?: 0;
?>
<div class="product-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tab-main" aria-controls="tab-main" role="tab" data-toggle="tab">Основное</a></li>
            <li role="presentation"><a href="#tab-data" aria-controls="tab-data" role="tab" data-toggle="tab">Данные</a></li>
            <li role="presentation"><a href="#tab-images" aria-controls="tab-images" role="tab" data-toggle="tab">Изображения</a></li>
            <li role="presentation"><a href="#tab-attributes" aria-controls="tab-attributes" role="tab" data-toggle="tab">Характеристики</a></li>
            <li role="presentation"><a href="#seo" aria-controls="messages" role="tab" data-toggle="tab">SEO</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="tab-main">
                <div class="box-body">
                    <?php if (!empty($languages)): ?>
                        <ul class="nav nav-tabs" id="language">
                            <?php foreach ($languages as $language): ?>
                                <li role="presentation"><a href="#language<?= $language['language_id'] ?>" data-toggle="tab">
                                    <img src="<?= Language::getImageUrl($language['image'], 16, 16) ?>" title="<?= $language['name'] ?>" /> <?= $language['name'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tab-content">
                            <?php foreach ($descriptions as $key => $description): ?>
                                <div role="tabpanel" class="tab-pane active" id="language<?= $key ?>">
                                    <div class="box-body">
                                        <?= $form->field($description, 'name')->textInput([
                                            'id' => 'classic-description-name-' . $key,
                                            'name' => 'ClassicDescription[' . $key . '][name]',
                                        ]) ?>

                                        <?= $form->field($description, 'description')->widget(CKEditor::class, [
                                            'options' => [
                                                'id' => 'classic-description-description-' . $key,
                                                'name' => 'ClassicDescription[' . $key . '][description]',
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
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
                    <?php endif; ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab-data">
                <div class="box-body">

                    <?= $form->field($product, 'price')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($product, 'price2')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($product, 'packaging_price')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($product, 'packaging_price2')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($product, 'status')->dropDownList(Classic::getStatusesList()) ?>

                    <?= $form->field($product, 'pb_id')->textInput() ?>

                    <?= $form->field($product, 'pb_big_id')->textInput(['maxlength' => true]) ?>

                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab-images">
                <div class="box-body table-responsive">
                    <div id="product-images-grid-view" class="grid-view">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <td class="text-left"><strong><?= $product->getAttributeLabel('image') ?><strong></td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="text-left">
                                    <a href="" id="thumb-image" data-toggle="product-image" class="img-thumbnail" style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;">
                                        <img src="<?= $product::getImageUrl($product->image, 600, 600) ?>" alt="" title="" data-placeholder="<?= $placeholder ?>" class="image-thumbnail" style="max-width: 100%; max-height: 100%;" />
                                    </a>
                                    <input type="hidden" name="Product[image]" value="<?= $product->image ?>" id="input-image" />
                                    <input type="file" accept="image/*" id="input-file-image" class="input-file-image" name="Classic[imageFile]" value="" onchange="onImageChange(this)" style="display: none" />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab-attributes">
                <div class="box-body">
                    <?php $product->properties = json_decode($product->properties, true); ?>
                    <?php
                    $languagesData = [];
                    foreach ($languages as $language) {
                        $language['icon'] = Language::getImageUrl($language['image'], 16, 16);
                        $languagesData[] = $language;
                    }
                    ?>
                    <product-properties-component
                        :properties="<?= htmlspecialchars(json_encode($product->isNewRecord ? [] : $product->properties)) ?>"
                        :languages="<?= htmlspecialchars(json_encode($languagesData)) ?>"
                        :model_name="'Classic'"
                    >
                    </product-properties-component>
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
    $('#language-data a:first').tab('show');
    $('#language-seo a:first').tab('show');
    
    function onImageChange(item) {
        if (!item.value) {
            return;
        }

        var src = window.URL.createObjectURL(item.files[0]);
        
        $(item).closest('tr').find('.image-thumbnail').attr('src', src);
    }
    ",
    View::POS_END,
    'script'
);
?>
