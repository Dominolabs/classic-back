<?php

use app\module\admin\models\Language;
use app\module\admin\module\event\models\Event;
use app\module\admin\module\event\models\EventCategory;
use app\module\admin\module\event\models\Tag;
use app\module\admin\module\gallery\models\Album;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\event\models\Event | app\components\ImageBehavior */
/* @var $descriptions array */
/* @var $languages array */
/* @var $descriptions array */
/* @var $eventImagesDataProvider yii\data\ArrayDataProvider */
/* @var $placeholder string */
/* @var $errors array */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="event-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">

    <?php if (!empty($languages)): ?>
        <ul class="nav nav-tabs" id="language">
            <?php foreach ($languages as $language): ?>
                <li role="presentation"><a href="#language<?= $language['language_id'] ?>" data-toggle="tab"><img
                                src="<?= Language::getImageUrl($language['image'], 16, 16) ?>"
                                title="<?= $language['name'] ?>"/> <?= $language['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="tab-content">
            <?php foreach ($descriptions as $key => $description): ?>
                <div role="tabpanel" class="tab-pane active" id="language<?= $key ?>">
                    <div class="box-body">
                        <?= $form->field($description, 'name')->textInput([
                            'id' => 'event-description-name-' . $key,
                            'name' => 'EventDescription[' . $key . '][name]',
                        ]) ?>

                        <?= $form->field($description, 'date')->textInput([
                            'id' => 'event-description-name-' . $key,
                            'name' => 'EventDescription[' . $key . '][date]',
                            'type' => 'date',
                            'value' => $description->date ? Yii::$app->formatter->asDate($description->date, 'php:Y-m-d') : date('Y-m-d')
                        ]) ?>

                        <?= $form->field($description, 'text')->widget(CKEditor::class, [
                            'options' => [
                                'id' => 'event-description-content-' . $key,
                                'name' => 'EventDescription[' . $key . '][text]',
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
        <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более
            языков!</p>
    <?php endif; ?>

    <div class="form-group field-event-image">

        <?= Html::label($model->getAttributeLabel('image'), 'input-file-image', ['class' => 'control-label', 'style' => 'display: block;']) ?>

        <a href="" id="thumb-media" data-toggle="event-image" class="img-thumbnail"
           style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center;">
            <img src="<?= $model->resizeImage($model->image, 100, 100) ?>" alt="" title="" class="image-thumbnail"
                 style="max-width: 100%; max-height: 100%;" data-placeholder="<?= $placeholder ?>"/>
        </a>
        <input type="hidden" name="Event[image]" value="<?= $model->image ?>" id="input-image"/>

        <input type="file" accept="image/*" id="input-file-image" class="input-file-image" name="Event[imageFile]"
               value="" onchange="onImageChange(this)" style="display: none"/>
    </div>

    <?= $form->field($model, 'videoUrls')->textInput() ?>

    <?= $form->field($model, 'gallery_id')->dropDownList(Album::getAlbumList(), []) ?>

    <?= $form->field($model, 'event_category_id')->dropDownList(EventCategory::getList()) ?>

    <?= $form->field($model, 'status')->dropDownList(Event::getStatusesList()) ?>

    <?= $form->field($model, 'sort_order')->textInput() ?>

    <?= $form->field($model, 'title')->textInput() ?>

    <?= $form->field($model, 'description')->textInput() ?>

    <?= $form->field($model, 'slug')->textInput() ?>

    <?php
    if (isset($model->tags) && !empty($model->tags)) {
        $tags_for_component = [];
        foreach ($model->tags as $tag) {
            $item = new stdClass();
            $item->tag_id = (string)$tag->tag_id;
            $item->name = $tag->tagDescription->name;
            $tags_for_component[] = $item;
        }
    }
    $current_tags = $tags_for_component ?? [];
    $tags = Tag::getListWithNames();

    $languagesData = [];
    foreach ($languages as $language) {
        $language['icon'] = Language::getImageUrl($language['image'], 16, 16);
        $languagesData[] = $language;
    }
    ?>
    <event-tags-component
            :current_tags="<?= htmlspecialchars(json_encode($current_tags)) ?>"
            :tags="<?= htmlspecialchars(json_encode($tags)) ?>"
            :languages="<?= htmlspecialchars(json_encode($languagesData)) ?>"
            :model_name="'Event'"
    ></event-tags-component>
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
