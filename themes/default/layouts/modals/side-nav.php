<?php

use app\module\admin\models\Language;
use app\module\admin\models\SeoUrl;
use yii\helpers\Url;
use app\widgets\LanguagePicker;

$hotelPageId = isset(Yii::$app->params['hotelPageId']) ? Yii::$app->params['hotelPageId'] : null;

$languageId = Language::getLanguageIdByCode(Yii::$app->language);
$hotelPageSlug = SeoUrl::getKeywordByQuery('page_id=' . $hotelPageId, $languageId);

/* @var $this yii\web\View */
?>
<section class="side-nav  modal" data-modal="side-nav">
    <div class="side-nav__content  modal__content">
        <a class="side-nav__mob-close  js-modal-close">
            <svg width="26px" height="26px">
                <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-close"></use>
            </svg>
        </a>
        <a href="<?= Url::to(['/']) ?>" class="side-nav__logo  logo">
            <img src="<?= Url::to('/img/logo/mojo_logo.svg') ?>" alt="<?= Yii::$app->params['siteName'] . ' logo' ?>" class="logo__img">
        </a>
        <div class="side-nav__main-content">
            <div class="side-nav__booking-wrap">
                <a href="" class="side-nav__book-btn  btn" data-modal-link="delivery"><?= Yii::t('order', 'Забронювати стіл') ?></a>
                <a href="" class="side-nav__book-btn  btn" data-modal-link="booking"><?= Yii::t('order', 'Замовити доставку') ?></a>
                <a href="<?= Url::to(['/' . $hotelPageSlug]) ?>" class="side-nav__book-btn  btn"><?= Yii::t('order', 'Забронювати номер') ?></a>
            </div>
            <?= $this->render('_menu') ?>
            <?= LanguagePicker::widget() ?>
            <a href="http://devseonet.com" class="side-nav__developed-by">
                <svg>
                    <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-devseonet"></use>
                </svg>
            </a>
        </div>
    </div>
</section>
