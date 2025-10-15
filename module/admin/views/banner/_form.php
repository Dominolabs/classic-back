<?php
/**
 * Create/update form view.
 */

use app\components\ImageBehavior;
use app\module\admin\models\Banner;
use app\module\admin\models\BannerImage;
use app\module\admin\models\Language;

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$bannerImageRow = [];

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Banner */
/* @var $form yii\widgets\ActiveForm */
/* @var $languages array */
/* @var $dataProviders array */
/* @var $placeholder string */
/* @var $errors array */
?>

<div class="banner-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
    ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'status')->dropDownList(Banner::getStatusesList()) ?>

        <?php if (!empty($languages)): ?>
        <ul class="nav nav-tabs" id="language">
            <?php foreach ($languages as $language): ?>
                <li role="presentation"><a href="#language<?= $language['language_id'] ?>" data-toggle="tab"><img src="<?= Language::getImageUrl($language['image'], 16, 16) ?>" title="<?= $language['name'] ?>" /> <?= $language['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content">
            <?php foreach ($dataProviders as $key => $dataProvider): ?>
            <?php $bannerImageRow[$key] = 0 ?>
            <div role="tabpanel" class="tab-pane active" id="language<?= $key ?>">
                <div class="box-body table-responsive">
                    <div id="banner-images-grid-view-<?= $key ?>" class="grid-view">
                        <table id="banner-images-table-<?= $key ?>" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="th-required">Название</th>
                                    <th>Ссылка</th>
                                    <th style="text-align: center">Изображение</th>
                                    <th>Порядок сортировки</th>
                                    <th class="action-column">&nbsp;</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="4"></td>
                                    <td style="vertical-align: middle;">
                                        <button type="button" class="btn btn-primary" title="" onclick="addImage(<?= $key ?>);" data-toggle="tooltip" data-original-title="Добавить" data-pjax="0">
                                            <i class="fa fa-plus-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php /** @var $bannerImageModel BannerImage|ImageBehavior */  ?>
                                <?php foreach ($dataProvider->getModels() as $bannerImageModel): ?>
                                <tr id="image-row-<?= $key ?>-<?= $bannerImageRow[$key] ?>">
                                    <td style="vertical-align: middle;">
                                        <div class="<?= !empty($errors[$key]['BannerImage'][$bannerImageRow[$key]]['title']) ? 'input-has-error' : '' ?>">
                                            <input type="text" name="BannerImage[<?= $key ?>][<?= $bannerImageRow[$key] ?>][title]" value="<?= $bannerImageModel->title ?>" placeholder="Название" class="form-control" required />
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div class="<?= !empty($errors[$key]['BannerImage'][$bannerImageRow[$key]]['link']) ? 'input-has-error' : '' ?>">
                                            <input type="text" name="BannerImage[<?= $key ?>][<?= $bannerImageRow[$key] ?>][link]" value="<?= $bannerImageModel->link ?>" placeholder="Ссылка" class="form-control" />
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center; ">
                                        <a href="" id="thumb-image-<?= $key ?>-<?= $bannerImageRow[$key] ?>" data-toggle="image" data-language-id="<?= $key ?>" data-row-id="<?= $bannerImageRow[$key] ?>" class="img-thumbnail" style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center; margin: auto;">
                                            <img src="<?= $bannerImageModel->resizeImage($bannerImageModel->image, 100, 100) ?>" alt="" title="" class="image-thumbnail" style="max-width: 100%; max-height: 100%;" data-placeholder="<?= $placeholder ?>" />
                                        </a>
                                        <div class="<?= !empty($errors[$key]['BannerImage'][$bannerImageRow[$key]]['image']) ? 'input-has-error' : '' ?>">
                                            <input type="hidden" name="BannerImage[<?= $key ?>][<?= $bannerImageRow[$key] ?>][image]" value="<?= $bannerImageModel->image ?>" id="input-image-<?= $key ?>-<?= $bannerImageRow[$key] ?>" />
                                            <input type="file" accept="image/*" id="input-file-image-<?= $key ?>-<?= $bannerImageRow[$key] ?>" class="input-file-image" name="BannerImage[<?= $key ?>][<?= $bannerImageRow[$key] ?>][imageFile]" value="" onchange="onImageChange(this)" style="display: none" />
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div class="<?= !empty($errors[$key]['BannerImage'][$bannerImageRow[$key]]['sort_order']) ? 'input-has-error' : '' ?>">
                                            <input type="text" name="BannerImage[<?= $key ?>][<?= $bannerImageRow[$key] ?>][sort_order]" value="<?= $bannerImageModel->sort_order ?>" placeholder="Порядок сортировки" class="form-control" />
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <button type="button" onclick="$('#image-row-<?= $key ?>-<?= $bannerImageRow[$key] ?>, .tooltip').remove();" data-toggle="tooltip" title="Удалить" class="btn btn-danger">
                                            <i class="fa fa-minus-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php $bannerImageRow[$key]++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach ?>
        </div>
        <?php else: ?>
            <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
        <?php endif; ?>
    </div>
    <div class="box-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php $imageRowJson = json_encode($bannerImageRow); ?>
<?php $this->registerJs("$('#language a:first').tab('show');", View::POS_READY, 'script'); ?>
<?php $this->registerJs(
<<<"SCRIPT"
    var imageRow = $imageRowJson;
    
    function addImage(languageId) {
        html  = '<tr id="image-row-' + languageId + '-' + imageRow[languageId] + '">';
        html += '  <td style="vertical-align: middle;"><div class="field-bannerimage-title required"><input type="text" id="bannerimage-title" name="BannerImage[' + languageId + '][' + imageRow[languageId] + '][title]" value="" placeholder="Название" aria-required="true" class="form-control" required maxlength="255" /></div></td>';	
        html += '  <td style="vertical-align: middle;"><input type="text" name="BannerImage[' + languageId + '][' + imageRow[languageId] + '][link]" value="" placeholder="Ссылка" class="form-control" /></td>';	
        html += '  <td style="vertical-align: middle; text-align: center; "><a href="" id="thumb-image-' + languageId + '-' + imageRow[languageId] + '" data-toggle="image" data-language-id="' + languageId + '" data-row-id="' + imageRow[languageId] + '" class="img-thumbnail" style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center; margin: auto;"><img src="$placeholder" alt="" title="" class="image-thumbnail" style="max-width: 100%; max-height: 100%;" data-placeholder="$placeholder" /></a><input type="hidden" name="BannerImage[' + languageId + '][' + imageRow[languageId] + '][image]" value="" id="input-image-' + languageId + '-' + imageRow[languageId] + '" /><input type="file" accept="image/*" id="input-file-image-' + languageId + '-' + imageRow[languageId] + '" class="input-file-image" name="BannerImage[' + languageId + '][' + imageRow[languageId] + '][imageFile]" value="" onchange="onImageChange(this)" style="display: none" /></td>';
        html += '  <td style="vertical-align: middle;"><input type="text" name="BannerImage[' + languageId + '][' + imageRow[languageId] + '][sort_order]" value="1" placeholder="Порядок сортировки" class="form-control" /></td>';
        html += '  <td style="vertical-align: middle;"><button type="button" onclick="$(\'#image-row-' + languageId + '-' + imageRow[languageId] + ', .tooltip\').remove();" data-toggle="tooltip" title="Удалить" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
        html += '</tr>';
        
        $('#banner-images-table-' + languageId + ' tbody').append(html);
        
        imageRow[languageId] += 1;
    }

    function onImageChange(item) {
        if (!item.value) {
            return;
        }

        var src = window.URL.createObjectURL(item.files[0]);
        
        $(item).closest('tr').find('.image-thumbnail').attr('src', src);
    }


SCRIPT
, View::POS_END, 'script2'); ?>
