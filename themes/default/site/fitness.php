<?php

/* @var $this yii\web\View */
/* @var $page array */
/* @var $defaultPage array */
/* @var $fitnessBannerImages array */
/* @var $imageWidth int */
/* @var $imageHeight int */
/* @var $fitnessPageTariffsDescr string */
/* @var $fitnessPageTeamDescr string */
/* @var $eventCategoryId int */
/* @var $albumCategoryId int */
/** @var $fitnessPageSocialNetworkCategoryId int */
/* @var $team array */
/* @var $pageUrl string */
/* @var $galleryUrl string */

use app\module\admin\module\team\models\Team;
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
?>
<div class="page-content__content  section-page">
    <section class="section-page__first-block  page-content__first-block  first-block" style="background-image: url('<?= $this->params['backgroundImage'] ?>')">
        <div class="first-block__logo  sections-logo">
            <div class="sections-logo__logo">
                <svg class="sections-logo__logo--ico">
                    <use xlink:href="<?= Url::to('/img/svg/sprite-logos.svg') ?>#<?= $page['css_class'] ?>"></use>
                </svg>
                <?php if ($fitnessPageSocialNetworkCategoryId > 0): ?>
                    <?= SocialNetworks::widget(['socialNetworkCategoryId' => $fitnessPageSocialNetworkCategoryId]) ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section class="section-page__interior  interior">
        <div class="interior__descr-wrap">
            <h2 class="interior__title"><?= Yii::t('family', 'Інтер\'єр') ?></h2>
            <?= !empty($page['content']) ? $page['content'] : $defaultPage['content'] ?>
        </div>
        <?php if (!empty($fitnessBannerImages)): ?>
        <div class="interior__carousel-wrap">
            <div class="interior__carousel  owl-carousel">
                <?php foreach ($fitnessBannerImages as $fitnessBannerImage): ?>
                <div class="interior__carousel-item">
                    <div class="interior__carousel-img-wrap">
                        <img src="<?= BannerImage::getImageUrl($fitnessBannerImage['image'], 931, 650) ?>"
                             alt="<?= $fitnessBannerImage['title'] ?>"
                             class="interior__carousel-img">
                    </div>
                </div>
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
    <section class="section-page__rates  rates">
        <?= $fitnessPageTariffsDescr ?>
    </section>
    <section class="section-page__team  team">
        <h2 class="team__title"><?= Yii::t('fitness', 'Команда') ?></h2>
        <ul class="team__list">
            <?php foreach ($team as $teamMember): ?>
            <li class="team__item">
                <div class="team__img-wrap">
                    <img class="team__img" src="<?= Team::getImageUrl($teamMember['image'], 210, 210) ?>" />
                </div>
                <h3 class="team__post"><?= $teamMember['position'] ?></h3>
                <h4 class="team__name"><?= $teamMember['name'] ?></h4>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?= Events::widget(['eventCategoryId' => $eventCategoryId]) ?>
    <?= Gallery::widget(['albumCategoryId' => $albumCategoryId, 'pageUrl' => $pageUrl, 'galleryUrl' => $galleryUrl]) ?>
</div>
