<?php

/* @var $this yii\web\View */
/* @var $page array */
/* @var $defaultPage array */
/* @var $skyGardenBannerImages array */
/* @var $imageWidth int */
/* @var $imageHeight int */
/* @var $skyGardenPageMenuImage string */
/* @var $skyGardenPageMenuDescr string */
/* @var $eventCategoryId int */
/* @var $albumCategoryId int */
/** @var $skyGardenPageSocialNetworkCategoryId int */
/* @var $pageUrl string */
/* @var $galleryUrl string */

use app\module\admin\models\SettingForm;
use app\module\admin\models\BannerImage;
use app\module\admin\models\Page;
use app\widgets\Events;
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

$skyGardenMenuPdf = isset(Yii::$app->params['skyGardenMenuPdf']) ? Yii::$app->params['skyGardenMenuPdf'] : null;
?>
<div class="page-content__content  section-page">
    <section class="section-page__first-block  page-content__first-block  first-block" style="background-image: url('<?= $this->params['backgroundImage'] ?>')">
        <div class="first-block__logo  sections-logo">
            <div class="sections-logo__logo">
                <svg class="sections-logo__logo--ico">
                    <use xlink:href="<?= Url::to('/img/svg/sprite-logos.svg') ?>#<?= $page['css_class'] ?>"></use>
                </svg>
                <?php if ($skyGardenPageSocialNetworkCategoryId > 0): ?>
                <?= SocialNetworks::widget(['socialNetworkCategoryId' => $skyGardenPageSocialNetworkCategoryId]) ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section class="section-page__interior  interior">
        <div class="interior__descr-wrap">
            <h2 class="interior__title"><?= Yii::t('family', 'Інтер\'єр') ?></h2>
            <?= !empty($page['content']) ? $page['content'] : $defaultPage['content'] ?>
        </div>
        <?php if (!empty($skyGardenBannerImages)): ?>
        <div class="interior__carousel-wrap">
            <div class="interior__carousel  owl-carousel">
                <?php foreach ($skyGardenBannerImages as $skyGardenBannerImage): ?>
                <?php if (!empty($skyGardenBannerImage['image'])): ?>
                <div class="interior__carousel-item">
                    <div class="interior__carousel-img-wrap">
                        <img src="<?= BannerImage::getImageUrl($skyGardenBannerImage['image'], 931, 650) ?>"
                             alt="<?= $skyGardenBannerImage['title'] ?>"
                             class="interior__carousel-img">
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="interior__arrows  arrows  arrows--white">
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
    <section class="section-page__menu-block  menu-block">
        <h2 class="menu-block__title"><?= Yii::t('menu', 'Меню') ?></h2>
        <?php if (!empty(Yii::$app->params['skyGardenPageMenuImage'])): ?>
        <div class="menu-block__img-wrap">
            <img class="menu-block__img" src="<?= SettingForm::getImageUrl(Yii::$app->params['skyGardenPageMenuImage'], 713, 563) ?>" />
        </div>
        <?php endif; ?>
        <div class="menu-block__descr-wrap">
            <p class="menu-block__text"><?= $skyGardenPageMenuDescr ?></p>
            <?php if (!empty($skyGardenMenuPdf)): ?>
            <a class="menu-block__btn  btn  g-mob-hide" href="<?= Url::to('/'. $skyGardenMenuPdf) ?>" target="_blank">
                <?= Yii::t('family', 'Перейти в меню') ?>
            </a>
            <?php endif; ?>
        </div>
        <?php if (!empty($skyGardenMenuPdf)): ?>
        <a class="menu-block__btn  btn  g-large-hide  g-desk-hide  g-tablet-hide" href="<?= Url::to('/'. $skyGardenMenuPdf) ?>" target="_blank">
            <?= Yii::t('family', 'Перейти в меню') ?>
        </a>
        <?php endif; ?>
    </section>
    <?= Events::widget(['eventCategoryId' => $eventCategoryId]) ?>
    <?= Gallery::widget(['albumCategoryId' => $albumCategoryId, 'pageUrl' => $pageUrl, 'galleryUrl' => $galleryUrl]) ?>
</div>
