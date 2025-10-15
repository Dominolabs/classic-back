<?php

/* @var $this yii\web\View */
/* @var $page array */
/* @var $defaultPage array */
/* @var $hotelServices array */
/* @var $imageWidth int */
/* @var $imageHeight int */
/* @var $hotelPageHotelBlockImageUrl string */
/* @var $eventCategoryId int */
/* @var $hotelPageHotelRoomsBlockBannerImages array */
/* @var $hotelPageSocialNetworkCategoryId int */
/* @var $pageUrl string */
/* @var $hotelPageHotelRoomsBlockTitle string */
/* @var $hotelPageMapBlockTitle string */
/* @var $contactsMapCoordinates string */
/* @var $rooms array */

use app\module\admin\models\BannerImage;
use app\module\admin\models\Page;
use app\module\admin\module\hotelservice\models\Hotelservice;
use app\module\admin\module\room\models\Room;
use app\widgets\BookingForm;
use app\widgets\Reviews;
use app\widgets\Footer;
use app\widgets\SocialNetworks;
use yii\helpers\Url;

$this->title = $page['meta_title'] ? $page['meta_title'] :
    ($defaultPage['meta_title'] ? $defaultPage['meta_title'] : ($page['title'] ? $page['title'] : $defaultPage['title']));

$metaDescription = $page['meta_description'] ? $page['meta_description'] : $defaultPage['meta_description'];
$metaKeywords = $page['meta_keyword'] ? $page['meta_keyword'] : $defaultPage['meta_keyword'];

$this->params['metaDescription'] = $metaDescription;
$this->params['metaKeywords'] = $metaKeywords;
$this->params['backgroundImage'] = !empty($page['image']) ? Page::getImageUrl($page['image'], $imageWidth, $imageHeight) : '../img/jpg/main-bg.jpg';
$phone = !empty(Yii::$app->params['phone']) ? Yii::$app->params['phone'] : null;

?>
<div class="page-content__content  section-page">
    <section class="section-page__first-block  page-content__first-block  first-block"
             style="background-image: url('<?= $this->params['backgroundImage'] ?>')">
        <div class="first-block__logo  sections-logo">
            <div class="sections-logo__logo">
                <svg class="sections-logo__logo--ico">
                    <use xlink:href="<?= Url::to('/img/svg/sprite-logos.svg') ?>#<?= $page['css_class'] ?>"></use>
                </svg>
                <?php if ($hotelPageSocialNetworkCategoryId > 0): ?>
                    <?= SocialNetworks::widget(['socialNetworkCategoryId' => $hotelPageSocialNetworkCategoryId]) ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section class="section-page__booking-block  booking-block" id="booking-section">
        <?php if (!empty($hotelPageHotelBlockImageUrl)): ?>
        <div class="booking-block__col">
            <form method="GET" action="<?= Url::to(['/booking?']) ?>" class="booking-block__form  booking-form" role="search" data-booking>
                <h3 class="booking-form__title"><?= Yii::t('hotel', 'Онлайн бронювання') ?></h3>
                <?= BookingForm::widget() ?>
            </form>
            <div class="booking-block__photo-wrap">
                <img src="<?= $hotelPageHotelBlockImageUrl ?>" alt="Hotel room" class="booking-block__photo">
            </div>
        </div>
        <?php endif; ?>
        <div class="booking-block__col ">
            <div class="booking-block__tabs-wrap" data-tabs-container="booking-tabs">
                <div class="booking-block__tab active" data-tab="tab-1">
                    <div class="booking-block__text text">
                        <?= !empty($page['content']) ? $page['content'] : $defaultPage['content'] ?>
                    </div>
                </div>
                <div class="booking-block__tab" data-tab="tab-2">
                    <?php if (!empty($hotelPageHotelRoomsBlockTitle)): ?>
                    <div class="booking-block__text text">
                        <h2><?= $hotelPageHotelRoomsBlockTitle ?></h2>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($hotelPageHotelRoomsBlockBannerImages)): ?>
                    <div class="booking-block__carousel-wrap">
                        <div class="booking-block__carousel  owl-carousel">
                            <?php foreach ($hotelPageHotelRoomsBlockBannerImages as $hotelPageHotelRoomsBlockBannerImage): ?>
                            <a href="<?= BannerImage::getImageUrl($hotelPageHotelRoomsBlockBannerImage['image'], 931, 650) ?>" data-fancybox="booking-block"
                               class="booking-block__carousel-item">
                                <div class="booking-block__carousel-img-wrap">
                                    <img src="<?= BannerImage::getImageUrl($hotelPageHotelRoomsBlockBannerImage['image'], 931, 650) ?>" class="booking-block__carousel-img">
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <div class="booking-block__arrows  arrows  arrows--white">
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
                </div>
                <?php if (!empty($contactsMapCoordinates)): ?>
                <div class="booking-block__tab" data-tab="tab-3">
                    <?php if (!empty($hotelPageMapBlockTitle)): ?>
                    <div class="booking-block__text text">
                        <h2><?= $hotelPageMapBlockTitle ?></h2>
                    </div>
                    <?php endif; ?>
                    <div class="booking-block__map-inner">
                        <div class="booking-block__map" id="map" data-def-props='<?= $contactsMapCoordinates ?>'></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="booking-block__info-navs" data-tabs-btns-wrap="booking-tabs">
                <a href="#" class="booking-block__info-btn active" data-tab-btn="tab-1">
                    <svg>
                        <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-hotel"></use>
                    </svg>
                    <span><?= Yii::t('hotel', 'Готель') ?></span>
                </a>
                <a href="#" class="booking-block__info-btn" data-tab-btn="tab-2">
                    <svg>
                        <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-room"></use>
                    </svg>
                    <span><?= Yii::t('hotel', 'Номери') ?></span>
                </a>
                <a href="#" class="booking-block__info-btn" data-tab-btn="tab-3">
                    <svg>
                        <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-map-point"></use>
                    </svg>
                    <span><?= Yii::t('hotel', 'Карта') ?></span>
                </a>
            </div>
        </div>
    </section>

    <?php if (!empty($rooms)): ?>
    <section class="section-page__rooms-list rooms-list">
        <div class="rooms-list__header">
            <h2 id="rooms" class="rooms-list__title">
                <?= Yii::t('hotel', 'Номери') ?>
            </h2>
            <div class="rooms-list__arrows  arrows">
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
        <div class="rooms-list__carousel-wrap">
            <div class="rooms-list__carousel  owl-carousel">
                <?php foreach ($rooms as $room): ?>
                <?php
                    if (!empty($room['images'])) {
                        $roomImages = '["' . implode('","', $room['images']) . '"]';
                    } else {
                        $roomImages = '[]';
                    }
                ?>
                <article class="room">
                    <div class="room__img-wrap"
                         data-fancybox-images='<?= $roomImages ?>'>
                        <img src="<?= Room::getImageUrl($room['image'], 488, 345) ?>" class="room__img" alt="Main room image">
                    </div>
                    <div class="room__header">
                        <h3 class="room__title"><a href="#"><?= $room['name'] ?></a></h3>
                        <div class="room__price price">
                            <div class="price__inner">
                                <?php if (!empty($room['old_price'])): ?>
                                <div class="price__top">
                                    <div class="price__discount">-<?= number_format(($room['old_price'] - $room['price']) / $room['old_price'] * 100, 0, '.', '') ?>%</div>
                                    <div class="price__old">
                                        <span class="price__old-val"><?= Room::formatPrice($room['old_price']) ?></span>
                                        <span class="price__old-currency">₴</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($room['price'])): ?>
                                <div class="price__main">
                                    <div class="price__main-amount">
                                        <span class="price__main-val"><?= Room::formatPrice($room['price']) ?></span>
                                        <span class="price__main-currency">₴</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($room['description'])): ?>
                    <p class="room__text">
                        <?= $room['description'] ?>
                    </p>
                    <?php endif; ?>
                    <ul class="room__params">
                        <?php if (!empty($room['area'])): ?>
                        <li class="room__params-item _text" title="<?= Yii::t('hotel', 'Площа') ?>">
                            <b><?= $room['area'] ?></b>
                            <span><?= Yii::t('hotel', 'кв. м') ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <a href="<?= Url::to('booking/' . $room['room_url']) ?: '#' ?>" class="room__book-btn  btn"><?= Yii::t('hotel', 'Детальніше') ?></a>
                </article>
                <?php endforeach; ?>
            </div
        </div>
    </section>
    <?php endif; ?>
    <?php if (!empty($hotelServices)): ?>
    <section class="section-page__services  services">
        <h2 class="services__title"><?= Yii::t('hotel', 'Додаткові послуги') ?></h2>
        <div class="services__carousel owl-carousel">
            <?php foreach ($hotelServices as $hotelService): ?>
                <div class="services__item">
                    <div class="services__item-img-wrap">
                        <img src="<?= Hotelservice::getImageUrl($hotelService['image'], 210, 210) ?>"
                             class="services__item-img" alt="service">
                    </div>
                    <h3 class="services__item-title"><?= $hotelService['name'] ?></h3>
                    <p class="services__item-text"><?= $hotelService['description'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    <?= Reviews::widget(['eventCategoryId' => $eventCategoryId]) ?>
    <?= Footer::widget() ?>
</div>