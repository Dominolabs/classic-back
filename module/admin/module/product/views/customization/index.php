<?php

use app\module\admin\module\event\models\EventCategory;
use app\module\admin\module\gallery\models\AlbumCategory;
use app\module\admin\models\SocialNetworkCategory;
use app\module\admin\models\Banner;
use app\module\admin\models\Language;
use app\module\admin\models\Page;
use app\module\admin\module\product\models\Product;
use app\module\admin\module\tariff\models\TariffCategory;
use dosamigos\ckeditor\CKEditor;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\SettingForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $languages array */
/* @var $placeholder string */

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
$addBadgeUrl = Url::to('/admin/product/customization/add-badge');
$deleteBadgeUrl = Url::to('/admin/product/customization/delete-badge');
?>


<div class="setting-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">
        <fieldset>
            <legend>Бейджи</legend>
        </fieldset>


        <!--Table-->
        <?php Pjax::begin(['id' => 'badges-pjax-container']) ?>
        <div class="box-body">
            <?php if (!empty($languages)): ?>
            <table class="table table-striped table-bordered" id="badges">
                <?php if (!empty($model->productBadges)): ?>
                    <?php foreach ($model->productBadges as $key => $badge):?>
                    <tr>
                        <td style="width: 90%">
                            <ul class="nav nav-tabs" id="badge-<?=$key?>">
                        <?php foreach ($languages as $language): ?>
                            <li role="presentation"<?= $language['language_id'] == 1 ? ' class="active"' : '' ?>><a href="#badge-<?=$key?>-<?= $language['language_id'] ?>" data-toggle="tab"><img src="<?= Language::getImageUrl($language['image'], 16, 16) ?>" title="<?= $language['name'] ?>"/><?= $language['name'] ?></a></li>
                        <?php endforeach; ?>
                        </ul>
                            <div class="tab-content">
                            <?php foreach ($languages as $language): ?>
                            <div role="tabpanel" class="tab-pane<?= $language['language_id'] == 1 ? ' active' : '' ?>" id="badge-<?=$key?>-<?= $language['language_id'] ?>">

                                <div class="box-body">
                                    <div class="form-group field-settingform-productbadges-<?= $key . '-name-' . $language['language_id'] ?>">
                                        <label for="settingform-productbadges-<?= $key . '-name-' . $language['language_id'] ?>"class="control-label">
                                            Назва бейджа
                                        </label>
                                        <input type="text"
                                               id="settingform-productbadges-<?= $key . '-name-' . $language['language_id'] ?>"
                                               name="SettingForm[productBadges][<?= $key ?>][name][<?= $language['language_id'] ?>]"
                                               value="<?= $badge['name'][$language['language_id']] ?? '' ?>"
                                               maxlength="255"
                                               class="form-control">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                            </div>
                    <?php endforeach ?>
                        </div>
                        </td>
                        <td>
                            <div class="text-right">
                                <?= Html::button('<i class="fa fa-minus-circle"></i> Удалить', ['class' => 'btn btn-danger btn-delete-badge', 'data-badge-key' => $key]) ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach ?>
                <?php endif ?>
            </table>
            <?php else: ?>
                <p>
                    <?= Html::a('Активируйте', ['/admin/language/index']) ?>или добавьте, пожалуйста, один или более
                    языков!</p>
            <?php endif; ?>
        </div>
        <?php Pjax::end() ?>
        <!--        End Table-->

        <fieldset>
            <legend>Добавить</legend>
        </fieldset>

        <?php if (!empty($languages)): ?>
            <ul class="nav nav-tabs" id="new-badge">
                <?php foreach ($languages as $language): ?>
                    <li role="presentation"<?= $language['language_id'] == 1 ? ' class="active"' : '' ?>><a
                                href="#new-badge-<?= $language['language_id'] ?>" data-toggle="tab"><img
                                    src="<?= Language::getImageUrl($language['image'], 16, 16) ?>"
                                    title="<?= $language['name'] ?>"/> <?= $language['name'] ?></a></li>
                <?php endforeach; ?>
            </ul>
            <div class="tab-content">
                <?php foreach ($languages as $language): ?>
                    <div role="tabpanel" class="tab-pane<?= $language['language_id'] == 1 ? ' active' : '' ?>"
                         id="new-badge-<?= $language['language_id'] ?>">
                        <div class="box-body">

                            <div class="form-group new-badge">
                                <label for="settingform-productbadges-new-badge-name-<?= $language['language_id'] ?>"
                                       class="control-label">
                                    Назва бейджа
                                </label>
                                <input type="text"
                                       id="settingform-productbadges-new-badge-name-<?= $language['language_id'] ?>"
                                       value="" maxlength="255"
                                       class="form-control">
                                <div class="help-block"></div>
                            </div>

                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="text-right">
                <?= Html::button('<i class="fa fa-plus-circle"></i> Добавить', ['class' => 'btn btn-primary btn-add-badge']) ?>
            </div>
        <?php else: ?>
            <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более
                языков!</p>
        <?php endif; ?>

    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
if (empty($languages)) $languages = [];
$this->registerJs(
    "   
   let loading = false;
   
    $('.btn-add-badge').on('click', function() {
        if (loading) return;
        let badge = {},
            names = {},
            languages = " . json_encode($languages) .";
        for (let i = 0; i < languages.length; i++) {
           names[languages[i].language_id] = $('#settingform-productbadges-new-badge-name-' + languages[i].language_id).val();
        }
        badge.name = names;
    
        loading = true;
        $.ajax('$addBadgeUrl', {
            type: 'POST',
            'data': {
                'badge': badge, 
            }
        }).done(function(data) {
            $.pjax.reload({container: '#badges-pjax-container'});
            for (let i = 0; i < languages.length; i++) {
                $('#settingform-productbadges-new-badge-name-' + languages[i].language_id).val('');
            }
            loading = false;
        });
    });
    
    $(document).on('click', '.btn-delete-badge', function(e) {
        if (loading) return;
        if (!confirm('Вы уверены, что хотите удалить этот елемент?')) return;
        let key = $(this).data('badgeKey');
        loading = true;
        $.ajax('$deleteBadgeUrl', {
            type: 'POST',
            'data': {
                'key': key, 
            }
        }).done(function(data) {
            $.pjax.reload({container: '#badges-pjax-container'});
            loading = false;
        });
    });
    ",
    View::POS_READY,
    'script'
);
?>
