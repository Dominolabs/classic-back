<?php

use app\module\admin\models\DesignForm;
use app\module\admin\models\Language;
use app\module\admin\models\SeoUrl;
use yii\helpers\Url;

/* @var $this \yii\web\View */

$languageId = Language::getLanguageIdByCode(Yii::$app->language);
$defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());

$slogan = !empty(Yii::$app->params['slogan'][$languageId]) ? Yii::$app->params['slogan'][$languageId]
    : (!empty(Yii::$app->params['slogan'][$defaultLanguageId]) ? Yii::$app->params['slogan'][$defaultLanguageId] : '');
$logo = !empty(Yii::$app->params['logo'][$languageId]) ? Yii::$app->params['logo'][$languageId]
    : (!empty(Yii::$app->params['logo'][$defaultLanguageId]) ? Yii::$app->params['logo'][$defaultLanguageId] : '');
$logoUrl = DesignForm::getImageUrl($logo, 384, 84);


$hotelPageId = isset(Yii::$app->params['hotelPageId']) ? Yii::$app->params['hotelPageId'] : null;
$languageId = Language::getLanguageIdByCode(Yii::$app->language);
$hotelPageSlug = SeoUrl::getKeywordByQuery('page_id=' . $hotelPageId, $languageId);

?>
<header class="mob-header  page-content__mob-header">
    <div class="mob-header__first-row">
        <div class="mob-header__side-holder"></div>
        <a href="<?= Url::to(['/']) ?>" class="mob-header__logo  logo">
            <img src="<?= Url::to('/img/logo/mojo_logo.svg') ?>" alt="<?= Yii::$app->params['siteName'] . ' logo' ?>" class="logo__img">
        </a>
        <a class="mob-header__menu-link" data-modal-link="side-nav">
            <svg width="29px" height="17px">
                <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-menu"></use>
            </svg>
        </a>
    </div>
    <div class="mob-header__booking-wrap">
        <a href="" class="mob-header__book-btn  btn" data-modal-link="delivery"><?= Yii::t('order', 'Забронювати стіл') ?></a>
        <a href="" class="mob-header__book-btn  btn" data-modal-link="booking"><?= Yii::t('order', 'Замовити доставку') ?></a>
        <a href="<?= Url::to(['/' . $hotelPageSlug]) ?>" class="mob-header__book-btn  btn"><?= Yii::t('order', 'Забронювати номер') ?></a>
    </div>
</header>
