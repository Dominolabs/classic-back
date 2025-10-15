<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<section class="side-nav  modal  modal-delivery" data-modal="delivery">
    <div class="side-nav__content  modal__content">
        <a class="side-nav__mob-close  modal__close  js-modal-close">
            <svg width="26px" height="26px">
                <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-close"></use>
            </svg>
        </a>
        <a href="<?= Url::to(['/']) ?>" class="side-nav__logo  logo">
            <img src="<?= Url::to('/img/logo/mojo_logo.svg') ?>" alt="<?= Yii::$app->params['siteName'] . ' logo' ?>" class="logo__img">
        </a>
        <div class="side-nav__main-content">
            <h2 class="modal__title  modal-delivery__title"><?= Yii::t('order', 'Забронювати стіл') ?></h2>
            <p class="modal-delivery__descr"><?= Yii::t('order', 'Щоб забронювати стіл Вам необхідно зателефонувати за номером:') ?></p>
            <a href="tel:<?= preg_replace('/\D+/', '', !empty(Yii::$app->params['phone']) ? Yii::$app->params['phone'] : ''); ?>" class="modal__contact">
                <svg width="24px" height="24px">
                    <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-phone"></use>
                </svg>
                <span><?= !empty(Yii::$app->params['phone']) ? Yii::$app->params['phone'] : '' ?></span>
            </a>
        </div>
    </div>
</section>