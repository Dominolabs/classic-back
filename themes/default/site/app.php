<?php

use yii\helpers\Url;

/* @var $address string */
/* @var $contactsMapSrc string */
/* @var $phone string */
/* @var $user \app\module\admin\models\User|null */
/* @var $this yii\web\View */

if ($user !== null) {
    $this->title = Yii::t('recommend', 'Ваш друг рекомендує') . ' "' . Yii::$app->params['siteName'] . '"';
} else {
    $this->title = Yii::t('recommend', 'Мобільний додаток') . ' ' . Yii::$app->params['siteName'];
}
?>
<div class="page-content__content  app-page">
    <div class="app-page__content">
        <h1 class="app-page__title"><?= $this->title ?></h1>
        <?php if ($user !== null): ?>
            <p class="app-page__descr">
                <?= Yii::t('recommend', 'Завантажте мобільний додаток, та введіть промо-код друга:')  ?>
            </p>
            <h3 class="app-page__code"><?= $user->promo_code ?></h3>
        <?php else: ?>
            <p class="app-page__descr">
                <?= Yii::t('recommend', 'Завантажте мобільний додаток.')  ?>
            </p>
        <?php endif; ?>
        <div class="app-page__btns-wrap">
            <a href="<?= !empty(Yii::$app->params['mobileAppIOS']) ? Yii::$app->params['mobileAppIOS'] : '' ?>" class="app-page__btn btn">
                <?= Yii::t('order', 'для IOS') ?>
            </a>
            <a href="<?= !empty(Yii::$app->params['mobileAppAndroid']) ? Yii::$app->params['mobileAppAndroid'] : '' ?>" class="app-page__btn btn">
                <?= Yii::t('order', 'для Android') ?>
            </a>
        </div>
        <footer class="app-page__footer">
            <p class="app-page__contact  app-page__contact--address">
                <svg width="16px" height="23px">
                    <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-map-point"></use>
                </svg>
                <?php if (!empty($address)): ?>
                <span><?= $address ?></span>
                <?php endif; ?>
            </p>
            <?php if (!empty($contactsMapSrc)): ?>
            <a href="<?= $contactsMapSrc ?>" class="app-page__map-link">
                <?= Yii::t('footer', 'Подивитися на карті') ?>
            </a>
            <?php endif; ?>
            <?php if (!empty($phone)): ?>
            <a href="tel:<?= preg_replace('/\D+/', '', $phone); ?>" class="app-page__contact">
                <svg width="24px" height="24px">
                    <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-phone"></use>
                </svg>
                <span><?= $phone ?></span>
            </a>
            <?php endif; ?>
        </footer>
    </div>
</div>
