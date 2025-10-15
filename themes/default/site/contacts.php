<?php

use app\module\admin\models\BannerImage;
use app\module\admin\models\Page;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $page array */
/* @var $defaultPage array */
/* @var $contactsBannerImages array */
/* @var $imageWidth int */
/* @var $imageHeight int */
/* @var $pageUrl string */
/* @var $galleryUrl string */

$this->title = $page['meta_title'] ? $page['meta_title'] :
    ($defaultPage['meta_title'] ? $defaultPage['meta_title'] : ($page['title'] ? $page['title'] : $defaultPage['title']));

$metaDescription = $page['meta_description'] ? $page['meta_description'] : $defaultPage['meta_description'];
$metaKeywords = $page['meta_keyword'] ? $page['meta_keyword'] : $defaultPage['meta_keyword'];

$this->params['metaDescription'] = $metaDescription;
$this->params['metaKeywords'] = $metaKeywords;
$this->params['backgroundImage'] = !empty($page['image']) ? Page::getImageUrl($page['image'], $imageWidth, $imageHeight) : '../img/jpg/main-bg.jpg';
$familyPageMenuPdf = isset(Yii::$app->params['familyPageMenuPdf']) ? Yii::$app->params['familyPageMenuPdf'] : null;
?>
<div class="page-content__content  section-page">
    <section class="section-page__contacts contacts">
        <div class="contacts__descr-wrap">
            <div class="contacts__descr text">
                <h2><?= Yii::t('contacts', 'Контакти') ?></h2>
                <?= !empty($page['content']) ? $page['content'] : $defaultPage['content'] ?>
            </div>
            <ul class="contacts__list">
                <?php if (!empty($address)): ?>
                <li class="contacts__list-item">
                    <svg>
                        <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-map-point"></use>
                    </svg>
                    <span class="contacts__list-text"><?= $address ?></span>
                </li>
                <?php endif; ?>
                <?php if (!empty($phone)): ?>
                <li class="contacts__list-item">
                    <svg>
                        <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-phone"></use>
                    </svg>
                    <span class="contacts__list-text">
                        <a href="tel:<?= preg_replace('/\D+/', '', $phone); ?>">
                            <?= $phone ?>
                        </a>
                    </span>
                </li>
                <?php endif; ?>
                <?php if (!empty($email)): ?>
                <li class="contacts__list-item">
                    <svg>
                        <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-email"></use>
                    </svg>
                    <span class="contacts__list-text"><a href="mailto:<?= $email ?>"><?= $email ?></a></span>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php if (!empty($contactsBannerImages)): ?>
        <div class="contacts__carousel-wrap">
            <div class="contacts__carousel  owl-carousel">
                <?php foreach ($contactsBannerImages as $contactsBannerImage): ?>
                <div class="contacts__carousel-item">
                    <div class="contacts__carousel-img-wrap">
                        <img src="<?= BannerImage::getImageUrl($contactsBannerImage['image'], 931, 650) ?>"
                             alt="<?= $contactsBannerImage['title'] ?>"
                             class="contacts__carousel-img">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="contacts__arrows  arrows  arrows--white">
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
        <?php endif; ?>
    </section>
    <?php if (!empty($contactsMapCoordinates)): ?>
    <div class="section-page__map-block map-block">
        <div class="map-block__inner">
            <div class="map-block__map" id="map" data-def-props='<?= $contactsMapCoordinates ?>'></div>
        </div>
    </div>
    <?php endif; ?>
</div>
