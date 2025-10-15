<?php

use app\module\admin\models\SettingForm;
use app\module\admin\module\event\models\EventCategory;
use app\module\admin\module\gallery\models\AlbumCategory;
use app\module\admin\models\SocialNetworkCategory;
use app\module\admin\models\Banner;
use app\module\admin\models\Language;
use app\module\admin\models\Page;
use app\module\admin\module\product\models\Category;
use app\module\admin\module\tariff\models\TariffCategory;
use dosamigos\ckeditor\CKEditor;
use kartik\select2\Select2;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\SettingForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $languages array */
/* @var $placeholder string */
/* @var $placeholder_banner string */

$this->title = 'Общие настройки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="setting-form box box-primary">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a></li>
            <li role="presentation"><a href="#contacts" aria-controls="contacts" role="tab" data-toggle="tab">Контакты</a></li>
            <li role="presentation"><a href="#pages" aria-controls="pages" role="tab" data-toggle="tab">Страницы</a></li>
            <li role="presentation"><a href="#push-notification" aria-controls="ush-notification" role="tab" data-toggle="tab">PUSH-уведомления</a></li>
            <li role="presentation"><a href="#mail" aria-controls="mail" role="tab" data-toggle="tab">Почта</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="main">
                <div class="box-body">

                    <?= $form->field($model, 'siteName')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'languageId')->dropDownList(Language::getList()) ?>

                    <?php if (!empty($languages)): ?>
                        <ul class="nav nav-tabs" id="setting-slogan">
                            <?php foreach ($languages as $language): ?>
                                <li role="presentation"><a href="#setting-slogan-<?= $language['language_id'] ?>" data-toggle="tab"><img src="<?= Language::getImageUrl($language['image'], 16, 16) ?>" title="<?= $language['name'] ?>" /> <?= $language['name'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tab-content">
                            <?php foreach ($languages as $language): ?>
                                <div role="tabpanel" class="tab-pane active" id="setting-slogan-<?= $language['language_id'] ?>">
                                    <div class="box-body">
                                        <?= $form->field($model, "titlePrefix" . "[" . $language['language_id'] . "]") ?>

                                        <?= $form->field($model, "titlePostfix" . "[" . $language['language_id'] . "]") ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
                    <?php endif; ?>

                    <?= $form->field($model, 'webCamPassword')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'adminEmailVacancy')->textInput(['maxlength' => true]) ?>

                    <fieldset>
                        <legend>LiqPay</legend>

                        <?= $form->field($model, 'availableOnlinePay')->checkbox(['true', 'false']) ?>

                        <?= $form->field($model, 'liqPayPublicKey')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'liqPayPrivateKey')->passwordInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'liqPaySandbox')->dropDownList([
                            0 => 'Включено',
                            1 => 'Отключено'
                        ]) ?>
                        <?= $form->field($model, 'liqPaySendRRO')->dropDownList([
                            0 => 'Отключено',
                            1 => 'Включено'
                        ]) ?>
                        <?= $form->field($model, 'liqPayEmailsForCheck')->textInput() ?>

                    </fieldset>

                    <fieldset>
                        <legend>Заказы</legend>

                        <?= $form->field($model, 'deliveryPrice')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'minKovelFreeDeliveryPrice')->textInput(['maxlength' => true]) ?>

                        <?php if (!empty($languages)): ?>
                            <ul class="nav nav-tabs" id="setting-orders">
                                <?php foreach ($languages as $language): ?>
                                    <li role="presentation"><a href="#setting-orders-<?= $language['language_id'] ?>" data-toggle="tab"><img src="<?= Language::getImageUrl($language['image'], 16, 16) ?>" title="<?= $language['name'] ?>" /> <?= $language['name'] ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="tab-content">
                                <?php foreach ($languages as $language): ?>
                                    <div role="tabpanel" class="tab-pane active" id="setting-orders-<?= $language['language_id'] ?>">
                                        <div class="box-body">
                                            <?= $form->field($model, "deliveryTime" . "[" . $language['language_id'] . "]") ?>

                                            <?= $form->field($model, "selfPickingTime" . "[" . $language['language_id'] . "]") ?>

                                            <?= $form->field($model, "deliveryDuration" . "[" . $language['language_id'] . "]") ?>

                                            <?= $form->field($model, "deliveryPriceOutsideKovel" . "[" . $language['language_id'] . "]") ?>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php else: ?>
                            <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
                        <?php endif; ?>


                        <?= $form->field($model, 'orderDefaultCityId')->dropDownList(\app\module\admin\module\order\models\City::getList()) ?>

                        <?= $form->field($model, 'minCookingTime')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 0]) ?>

                        <?= $form->field($model, 'minCookingTimeSelfPickup')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 0]) ?>

                        <?= $form->field($model, 'maxCountMainIngredients')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 0]) ?>

                        <?= $form->field($model, 'maxCountAdditionalIngredients')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 0]) ?>

                        <?= $form->field($model, 'homeCityId')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 1]) ?>

                        <?= $form->field($model, 'publicOfferPageId')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 1]) ?>

                        <?= $form->field($model, 'termsAndConditionsPageId')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 1]) ?>

                        <?= $form->field($model, 'isSelfPickingActionAvailable')->checkbox(['true', 'false']) ?>

                        <?= $form->field($model, 'selfPickingActionDiscount')->textInput(['maxlength' => true, 'type' => 'number', 'min' => 1]) ?>

                        <?= $form->field($model, 'pizzaCategoryId')->widget(Select2::class, [
                            'data' => Category::getList(),
                            'language' => 'ru',
                            'options' => ['placeholder' => 'Выберите категорию ...'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]) ?>

                        <?= $form->field($model, 'noodlesCategoryId')->widget(Select2::class, [
                            'data' => Category::getList(),
                            'language' => 'ru',
                            'options' => ['placeholder' => 'Выберите категорию ...'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]) ?>

                    </fieldset>

                    <fieldset>
                        <legend>Принтер</legend>

                        <?= $form->field($model, 'printerIp')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'printerPort')->textInput(['maxlength' => true]) ?>

                    </fieldset>

                    <fieldset>
                        <legend>Мобильные приложения</legend>

                        <?= $form->field($model, 'mobileAppIOS')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'mobileAppAndroid')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'minAppVersion')->textInput(['maxlength' => true]) ?>

                        <div class="form-group field-catering-page-menu-image">
                            <?= Html::label($model->getAttributeLabel('pizzaConstructorBanner'), 'input-file-image-4', ['class' => 'control-label', 'style' => 'display: block;']) ?>

                            <a href="#" id="thumb-image-4" data-toggle="setting-image" class="img-thumbnail" style="width: 310px; height: 70px; display: flex; align-items: center; justify-content: center;" data-row-id="4">
                                <img src="<?php try { echo $model->getBehavior('pizzaConstructorBanner')->resizeImage($model->pizzaConstructorBanner, 290, 60); } catch (Exception $exception) { echo $placeholder_banner; } ?>" alt="" title="" class="image-thumbnail" style="max-width: 100%; max-height: 100%;" data-placeholder="<?= $placeholder_banner ?>" />
                            </a>

                            <input type="hidden" name="SettingForm[pizzaConstructorBanner]" value="<?= $model->pizzaConstructorBanner ?>" id="input-catering-page-menu-image" />

                            <input type="file" accept="image/*" id="input-file-image-4" class="input-file-image-4" name="SettingForm[pizzaConstructorBannerImage]" onchange="onImageChange(this)" style="display: none" />

                            <button type="button" class="btn btn-danger btn-sm mt-2 remove-banner-btn"
                                    data-target="input-catering-page-menu-image"
                                    data-thumb="thumb-image-4"
                                    data-field="pizzaConstructorBanner"
                            >🗑 Видалити</button>
                        </div>

                        <div class="form-group field-catering-page-menu-image">
                            <?= Html::label($model->getAttributeLabel('pizzaConstructorBannerEn'), 'input-file-image-5', ['class' => 'control-label', 'style' => 'display: block;']) ?>

                            <a href="#" id="thumb-image-5" data-toggle="setting-image" class="img-thumbnail" style="width: 310px; height: 70px; display: flex; align-items: center; justify-content: center;" data-row-id="5">
                                <img src="<?php try { echo $model->getBehavior('pizzaConstructorBannerEn')->resizeImage($model->pizzaConstructorBannerEn, 290, 60); } catch (Exception $exception) { echo $placeholder_banner; } ?>" alt="" title="" class="image-thumbnail" style="max-width: 100%; max-height: 100%;" data-placeholder="<?= $placeholder_banner ?>" />
                            </a>

                            <input type="hidden" name="SettingForm[pizzaConstructorBannerEn]" value="<?= $model->pizzaConstructorBannerEn ?>" id="input-baner-en-page-menu-image" />

                            <input type="file" accept="image/*" id="input-file-image-5" class="input-file-image-5" name="SettingForm[pizzaConstructorBannerImageEn]" onchange="onImageChange(this)" style="display: none" />

                            <button type="button" class="btn btn-danger btn-sm mt-2 remove-banner-btn"
                                    data-target="input-baner-en-page-menu-image"
                                    data-thumb="thumb-image-5"
                                    data-field="pizzaConstructorBannerEn"
                            >🗑 Видалити</button>
                        </div>


                    </fieldset>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="contacts">
                <div class="box-body">
                    <?php if (!empty($languages)): ?>
                        <ul class="nav nav-tabs" id="setting-address">
                            <?php foreach ($languages as $language): ?>
                                <li role="presentation"><a href="#setting-address-<?= $language['language_id'] ?>" data-toggle="tab"><img src="<?= Language::getImageUrl($language['image'], 16, 16) ?>" title="<?= $language['name'] ?>" /> <?= $language['name'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="tab-content">
                            <?php foreach ($languages as $language): ?>
                                <div role="tabpanel" class="tab-pane active" id="setting-address-<?= $language['language_id'] ?>">
                                    <div class="box-body">
                                        <?= $form->field($model, "address" . "[" . $language['language_id'] . "]") ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <p><?= Html::a('Активируйте', ['/admin/language/index']) ?> или добавьте, пожалуйста, один или более языков!</p>
                    <?php endif; ?>

                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'contactsMapSrc')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'contactsMapCoordinates')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="pages">
                <div class="box-body">
                    <fieldset>
                        <legend>Главная страница</legend>

                        <?= $form->field($model, 'mainPageId')->dropDownList(Page::getList()) ?>

                        <?= $form->field($model, 'mainPageMenuBannerId')->dropDownList(Banner::getList()) ?>

                    </fieldset>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="push-notification">
                <div class="box-body">

                    <?= $form->field($model, 'notificationBirthDateBeforeWeek')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'notificationBirthDateBeforeDay')->textInput(['maxlength' => true]) ?>

                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="mail">
                <div class="box-body">

                    <?= $form->field($model, 'adminEmail')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'supportEmail')->textInput(['maxlength' => true]) ?>
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
    $('#setting a:first').tab('show');
    $('#setting-slogan a:first').tab('show');
    $('#setting-address a:first').tab('show');
    $('#setting-orders a:first').tab('show');
    $('#setting-main-page-carousel-descr a:first').tab('show');
    $('#setting-product-page-payment-delivery-descr a:first').tab('show');
    ",
    View::POS_READY,
    'script'
);
?>
<?php
$this->registerJs(<<<JS
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.remove-banner-btn');

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const inputId = this.getAttribute('data-target');
            const thumbId = this.getAttribute('data-thumb');
            // Беремо назву поля прямо з data-field!
            const fieldName = this.getAttribute('data-field');

            const input = document.getElementById(inputId);
            const thumb = document.getElementById(thumbId);
            const img = thumb.querySelector('img');
            const placeholder = img.getAttribute('data-placeholder');

            input.value = '';
            img.setAttribute('src', placeholder);

            fetch('/admin/setting/delete-image', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    field: fieldName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Помилка при видаленні зображення: ' + (data.message || 'невідома помилка'));
                }
            })
            .catch(err => {
                console.error('AJAX error:', err);
                alert('Помилка мережі при видаленні зображення');
            });
        });
    });
});

function onImageChange(item) {
    if (!item.value) {
        return;
    }

    var src = window.URL.createObjectURL(item.files[0]);
    $(item).closest('div').find('.image-thumbnail').attr('src', src);
}
JS
    , \yii\web\View::POS_END);
?>
