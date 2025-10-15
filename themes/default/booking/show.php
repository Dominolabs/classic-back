<?php

/* @var $page array */
/* @var $defaultPage array */
/* @var $room array */
/* @var $this yii\web\View */
use app\components\UrlHelper;
$this->title = $page['meta_title'] ? $page['meta_title'] :
    ($defaultPage['meta_title'] ? $defaultPage['meta_title'] : ($page['title'] ? $page['title'] : $defaultPage['title']));

$metaDescription = $page['meta_description'] ? $page['meta_description'] : $defaultPage['meta_description'];
$metaKeywords = $page['meta_keyword'] ? $page['meta_keyword'] : $defaultPage['meta_keyword'];

$this->params['metaDescription'] = $metaDescription;
$this->params['metaKeywords'] = $metaKeywords;
$is_from_show = true;
use app\widgets\BookingForm;
use yii\helpers\Url;
?>
<div class="page-content__content  section-page">
    <div class="section-page__booking-header booking-header">
        <div class="booking-header__title-wrap">
            <h1 class="booking-header__title"><?= $this->title ?></h1>
        </div>
        <form action="<?= Url::toRoute(['/booking'], true) ?>" class="booking-header__form booking-form" data-booking>
            <?= BookingForm::widget() ?>
        </form>
    </div>
    <div class="section-page__booking-list booking-list">
        <div class="booking-list__wrapper">
            <?= $this->render('_room', compact('room', 'is_from_show')) ?>
        </div>
        <a href="<?= Url::to(['/hotel#rooms']) ?>" class="reviews-list__add-btn  btn"><?= Yii::t('hotel', 'Переглянути інші номери') ?></a>
    </div>
</div>
