<?php

/* @var $this yii\web\View */
/* @var $page array */
/* @var $defaultPage array */
/* @var $cateringBannerImages array */
/* @var $imageWidth int */
/* @var $imageHeight int */
/* @var $cateringPageMenuImage string */
/* @var $cateringPageMenuDescr string */
/* @var $eventCategoryId int */
/* @var $albumCategoryId int */
/** @var $cateringPageSocialNetworkCategoryId int */
/* @var $tariffs array */
/* @var $pageUrl string */
/* @var $galleryUrl string */

use app\module\admin\models\SeoUrl;
use app\module\admin\models\SettingForm;
use app\module\admin\models\BannerImage;
use app\module\admin\models\Page;
use app\widgets\Events;
use app\widgets\Footer;
use app\widgets\Gallery;
use app\widgets\SocialNetworks;
use yii\helpers\Url;

$this->title = $page['meta_title'] ? $page['meta_title'] :
    ($defaultPage['meta_title'] ? $defaultPage['meta_title'] : ($page['title'] ? $page['title'] : $defaultPage['title']));

$metaDescription = $page['meta_description'] ? $page['meta_description'] : $defaultPage['meta_description'];
$metaKeywords = $page['meta_keyword'] ? $page['meta_keyword'] : $defaultPage['meta_keyword'];

$this->params['metaDescription'] = $metaDescription;
$this->params['metaKeywords'] = $metaKeywords;
$this->params['backgroundImage'] = !empty($page['image']) ? Page::getImageUrl($page['image'], $imageWidth, $imageHeight) : '../img/jpg/main-bg.jpg';

$cateringMenuPdf = isset(Yii::$app->params['cateringMenuPdf']) ? Yii::$app->params['cateringMenuPdf'] : null;
?>
<div class="page-content__content  section-page">
    <section class="section-page__first-block  page-content__first-block  first-block" style="background-image: url('<?= $this->params['backgroundImage'] ?>')">
        <div class="first-block__logo  sections-logo">
            <div class="sections-logo__logo">
                <svg class="sections-logo__logo--ico">
                    <use xlink:href="<?= Url::to('/img/svg/sprite-logos.svg') ?>#<?= $page['css_class'] ?>"></use>
                </svg>
                <?php if ($cateringPageSocialNetworkCategoryId > 0): ?>
                <?= SocialNetworks::widget(['socialNetworkCategoryId' => $cateringPageSocialNetworkCategoryId]) ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section class="section-page__hall-rates  hall-rates">
        <div class="hall-rates__titles-wrap">
            <h2 class="hall-rates__title"><?= Yii::t('event-hall', 'Тарифи') ?></h2>
            <ul class="hall-rates__tabs-btns-list">
                <?php foreach ($tariffs as $key => $tariff): ?>
                    <li class="hall-rates__tabs-btns-item">
                        <a class="hall-rates__tab-btn<?= ($key === 0) ? ' active' : '' ?>" data-hall-tab-btn="<?= SeoUrl::transliterate($tariff['name']) ?>">
                            <?= $tariff['name'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="hall-rates__tabs-container">
            <?php foreach ($tariffs as $key => $tariff): ?>
                <article class="hall-rates__tab<?= ($key === 0) ? ' active' : '' ?>" data-hall-tab="<?= SeoUrl::transliterate($tariff['name']) ?>">
                    <div class="hall-rates__carousel-wrap">
                        <div class="hall-rates__carousel  owl-carousel">
                            <?php foreach ($tariff['banner_images'] as $bannerImage): ?>
                                <div class="hall-rates__carousel-item">
                                    <div class="hall-rates__carousel-img-wrap">
                                        <img src="<?= BannerImage::getImageUrl($bannerImage['image'], 614, 658) ?>" class="hall-rates__carousel-img">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="hall-rates__arrows  arrows  arrows--white">
                        <span class="arrows__btn  arrows__btn--left  arrows__btn--disabled">
                            <svg>
                                <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-arrow-light"></use>
                            </svg>
                        </span>
                            <span class="arrows__btn  arrows__btn--right">
                            <svg>
                                <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-arrow-light"></use>
                            </svg>
                        </span>
                        </div>
                    </div>
                    <div class="hall-rates__text-wrap">
                        <h3 class="hall-rates__sub-title"><?= $tariff['name'] ?> <span class="hall-rates__sub-title--line"></span></h3>
                        <div class="hall-rates__text">
                            <?= $tariff['content'] ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="section-page__menu-block  menu-block">
        <h2 class="menu-block__title"><?= Yii::t('menu', 'Меню') ?></h2>
        <?php if (!empty(Yii::$app->params['cateringPageMenuImage'])): ?>
        <div class="menu-block__img-wrap">
            <img class="menu-block__img" src="<?= SettingForm::getImageUrl(Yii::$app->params['cateringPageMenuImage'], 713, 563) ?>" />
        </div>
        <?php endif; ?>
        <div class="menu-block__descr-wrap">
            <p class="menu-block__text"><?= $cateringPageMenuDescr ?></p>
            <?php if (!empty($cateringMenuPdf)): ?>
            <a class="menu-block__btn  btn  g-mob-hide" href="<?= Url::to('/'. $cateringMenuPdf) ?>" target="_blank">
                <?= Yii::t('family', 'Перейти в меню') ?>
            </a>
            <?php endif; ?>
        </div>
        <?php if (!empty($cateringMenuPdf)): ?>
        <a class="menu-block__btn  btn  g-large-hide  g-desk-hide  g-tablet-hide" href="<?= Url::to('/'. $cateringMenuPdf) ?>" target="_blank">
            <?= Yii::t('family', 'Перейти в меню') ?>
        </a>
        <?php endif; ?>
    </section>
    <?= Events::widget(['eventCategoryId' => $eventCategoryId]) ?>
    <?= Gallery::widget(['albumCategoryId' => $albumCategoryId, 'pageUrl' => $pageUrl, 'galleryUrl' => $galleryUrl]) ?>
</div>
