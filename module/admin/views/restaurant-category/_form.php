<?php

use app\module\admin\models\Language;
use app\module\admin\models\RestaurantCategory;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\RestaurantCategory */
/* @var $descriptions array */
/* @var $languages array */
/* @var $form yii\widgets\ActiveForm */
/* @var $seoUrls array */
?>
<div class="restaurant-category-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#main" aria-controls="home" role="tab" data-toggle="tab">Основное</a></li>
            <li role="presentation"><a href="#seo" aria-controls="messages" role="tab" data-toggle="tab">SEO</a></li>
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
                                        <?= $form->field($description, 'name')->textInput([
                                            'id' => 'restaurant-category-description-name-' . $key,
                                            'name' => 'RestaurantCategoryDescription[' . $key . '][name]',
                                        ]) ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
                    <?php endif; ?>

                    <?= $form->field($model, 'status')->dropDownList(RestaurantCategory::getStatusesList()) ?>

                    <?= $form->field($model, 'sort_order')->textInput() ?>
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

