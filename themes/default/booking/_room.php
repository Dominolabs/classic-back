<?php

use app\module\admin\module\room\models\Room;
use yii\helpers\Url;
use app\components\UrlHelper;

use function Couchbase\defaultDecoder;

/* @var $room array */
/* @var $nights_quantity */
/* @var $vocabulary */


if (!empty($room['images'])) {
    $roomImages = '["' . implode('","', $room['images']) . '"]';
} else {
    $roomImages = '[]';
}
$phone = !empty(Yii::$app->params['phone']) ? Yii::$app->params['phone'] : null;
$price = !empty($room['price']) ? Room::formatPrice($room['price']) : null;

$beds_quantity_title = !empty($room['double_beds_quantity']) ? $room['double_beds_quantity'] . ' ' . Yii::t('hotel', 'двомісне ліжко') . ' ' : '';
$beds_quantity_title .= !empty($room['single_beds_quantity']) ? $room['single_beds_quantity'] . ' ' . Yii::t('hotel', 'одномісне ліжко') : '';
$max_persons_quantity = ((int)$room['single_beds_quantity'] * 1) + ((int)$room['double_beds_quantity'] * 2);

$room_selected = isset($rooms_selected[$room['room_id']]) ? $rooms_selected[$room['room_id']] : 0 ;
$room_available = (int)$room['rooms_available'] - (int)$room_selected;

$all_cart_rooms = json_decode($cart->getContentJson(), true);
$such_rooms_in_cart = array_filter($all_cart_rooms, function($item) use($room){
   return (int)$item['id'] === (int)$room['room_id'];
});

?>
<div class="booking-list__item room-booking _slide _inline" data-room-id="<?= $room['room_id'] ?>">
   <div class="room-booking__main">
      <div class="room-booking__head">
         <div class="room-booking__media-wrap"
              style="background-image: url(<?= Room::getImageUrl($room['image'], 200, 141) ?>)"
              data-fancybox-images='<?= $roomImages ?>'>
            <svg class="room-booking__zoom">
               <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg#ico-zoom') ?>"></use>
            </svg>
         </div>
         <div class="room-booking__head-descr">
            <div class="room-booking__name-wrap">
               <h2 class="room-booking__name"><?= $room['name'] ?></h2>
               <div class="room-booking__placement  placement"
                    title="<?= $beds_quantity_title ?>"
                    data-tooltip>
                   <?php if (!empty((int)$room['double_beds_quantity'])): ?>
                       <?php for ($i = 0; $i < (int)$room['double_beds_quantity']; $i++): ?>
                         <svg>
                            <use
                               xlink:href="<?= UrlHelper::toAbsolute('img/svg/sprite-main.svg#ico-placement') ?>"></use>
                         </svg>
                         <svg>
                            <use
                               xlink:href="<?= UrlHelper::toAbsolute('img/svg/sprite-main.svg#ico-placement') ?>"></use>
                         </svg>
                           <?php if ($i !== $room['double_beds_quantity'] - 1): ?>
                            <span>+</span>
                           <?php endif; ?>
                       <?php endfor; ?>
                   <?php endif; ?>

                   <?php if (!empty((int)$room['double_beds_quantity']) && !empty((int)$room['single_beds_quantity'])): ?>
                      <span>+</span>
                   <?php endif; ?>

                   <?php if (!empty((int)$room['single_beds_quantity'])): ?>
                       <?php for ($i = 0; $i < (int)$room['single_beds_quantity']; $i++): ?>
                         <svg>
                            <use
                               xlink:href="<?= UrlHelper::toAbsolute('img/svg/sprite-main.svg#ico-placement') ?>"></use>
                         </svg>
                           <?php if ($i !== $room['single_beds_quantity'] - 1): ?>
                            <span>+</span>
                           <?php endif; ?>
                       <?php endfor; ?>
                   <?php endif; ?>
               </div>
            </div>
            <div class="room-booking__price  price">
               <div class="price__main-row">
                  <div class="price__inner">
                      <?php if (!empty($room['old_price'])): ?>
                         <div class="price__top">
                            <div class="price__discount">
                               -<?= number_format(($room['old_price'] - $room['price']) / $room['old_price'] * 100, 0, '.', '') ?>
                               %
                            </div>
                            <div class="price__old">
                               <span class="price__old-val"><?= Room::formatPrice($room['old_price']) ?></span>
                               <span class="price__old-currency">₴</span>
                            </div>
                         </div>
                      <?php endif; ?>
                      <?php if (!empty($price)): ?>
                         <div class="price__main">
                            <div class="price__main-quantity">
                               <span class="price__main-val"><?= $price ?></span>
                               <span class="price__main-currency">₴</span>
                            </div>
                            <p class="price__days">/ <?= Yii::t('hotel', 'Ніч') ?></p>
                         </div>
                      <?php endif; ?>
                  </div>
               </div>
            </div>
            <ul class="room-booking__params">
                <?php if (!empty($room['show_bed_icon'])): ?>
                   <li class="room-booking__params-item"
                       title="<?php echo $room['bed_icon_tooltip'] ?>"
                       <?php echo !empty($room['bed_icon_tooltip']) ? 'data-tooltip' : '' ?>
                   >
                      <svg>
                         <use xlink:href="<?= UrlHelper::toAbsolute('img/svg/sprite-main.svg#ico-room') ?>"></use>
                      </svg>
                   </li>
                <?php endif; ?>
                <?php if (!empty($room['area'])): ?>
                   <li class="room-booking__params-item _text"
                       title="<?php echo $room['area'] . ' ' . Yii::t('hotel', 'кв. м') ?>" data-tooltip>
                      <b><?= $room['area'] ?></b>
                      <span><?= Yii::t('hotel', 'кв. м') ?></span>
                   </li>
                <?php endif; ?>
                <?php if (!empty($room['show_location_icon'])): ?>
                   <li class="room-booking__params-item"
                       title="<?php echo $room['location_icon_tooltip'] ?>"
                       <?php echo !empty($room['location_icon_tooltip']) ? 'data-tooltip' : '' ?>
                   >
                      <svg>
                         <use xlink:href="<?= UrlHelper::toAbsolute('img/svg/sprite-main.svg#ico-map-point') ?>"></use>
                      </svg>
                   </li>
                <?php endif; ?>
                <?php if (!empty($room['show_hotel_icon'])): ?>
                   <li class="room-booking__params-item"
                       title="<?php echo $room['hotel_icon_tooltip'] ?>"
                       <?php echo !empty($room['hotel_icon_tooltip']) ? 'data-tooltip' : '' ?>
                   >
                      <svg>
                         <use xlink:href="<?= UrlHelper::toAbsolute('img/svg/sprite-main.svg#ico-hotel') ?>"></use>
                      </svg>
                   </li>
                <?php endif; ?>
            </ul>

         </div>

      </div>
      <div data-slide-toggle
           data-default-max-height="50"
           class="room-booking__descr-wrap">
          <?php if (!empty($room['description'])): ?>
             <div data-slide-toggle-block
                  style="max-height: 50px"
                  class="room-booking__descr">
                <div class="room-booking__descr-text text">
                    <?= $room['description'] ?>
                </div>
             </div>
          <?php endif; ?>
         <button type="button"
                 data-slide-toggle-btn
                 class="room-booking__descr-more">
            <span class="text-closed"><?= Yii::t('hotel', 'Детальніше') ?></span>
            <span class="text-open"><?= Yii::t('hotel', 'Згорнути') ?></span>
            <svg>
               <use xlink:href="<?= Url::to('./img/svg/sprite-main.svg#ico-arrow-down') ?>"></use>
            </svg>
         </button>
      </div>
   </div>

    <?php if (!isset($is_from_show)): ?>
       <div class="room-booking__booking-wrap"
            data-booking-item
            data-booking-days="<?php echo $nights_quantity ?>"
            data-booking-persons-quantity-max="<?php echo $max_persons_quantity ?>"
            data-booking-rooms_max-available-quantity="<?php echo $room['rooms_available'] ?>"
            data-booking-rooms-quantity-max="<?php echo $room_available ?>">
          <p class="room-booking__booking-free-rooms"><?= Yii::t('hotel', 'Вільних номерів') ?> <span
                class="g-text-bold"><?php echo $room_available ?></span></p>
          <div class="room-booking__booking-head">
             <h4 class="room-booking__booking-title">
                <span class="room-booking__booking-title--accent">
                   <span data-booking-price="<?= $room['price'] ?>"><?= $room['price'] ?></span>&nbsp;₴</span> /
                <span data-booking-roms-quantity class="room-noun-quantity"></span><span class="rooms-noun"
                                                                                      lang="<?php echo \Yii::$app->language ?>">&nbsp;<?= $vocabulary->get('номер', 1) ?></span>
                /
                 <?php echo $nights_quantity ?>&nbsp;<span data-booking-nights-quantity></span><span><?= $vocabulary->get('ніч', $nights_quantity) ?></span>
             </h4>
          </div>
          <div class="persons-quantity">
             <div class="persons-quantity__items-list" data-booking-rooms-list>

                 <div class="persons-quantity__item" data-booking-room>
                      <h5 class="persons-quantity__items-title"><?= Yii::t('hotel', 'Номер') ?> <span
                            data-booking-room-count>1</span>
                      </h5>
                      <button class="persons-quantity__items-delete  btn" data-booking-delete-room>
                         <svg>
                            <use xlink:href="<?= Url::to('./img/svg/sprite-main.svg#ico-trash') ?>"></use>
                         </svg>
                      </button>
                      <div class="persons-quantity__counters">
                         <div class="persons-quantity__counter-wrap">
                            <h6 class="persons-quantity__counter-title"><?= Yii::t('hotel', 'Дорослих') ?> (14+)</h6>
                            <div class="persons-quantity__counter  quantity"
                                 data-quantity
                                 data-quantity-min="1"
                                 data-quantity-max="4">
                               <button type="button" class="quantity__btn  _minus  btn" data-quantity-minus>-
                               </button>
                               <input type="text" class="quantity__value" value="1"
                                      data-adult
                                      readonly
                                      data-quantity-value>
                               <button type="button" class="quantity__btn  _plus  btn" data-quantity-plus>+
                               </button>
                            </div>
                         </div>
                         <div class="persons-quantity__counter-wrap">
                            <h6 class="persons-quantity__counter-title"><?= Yii::t('hotel', 'Дітей') ?></h6>
                            <div class="persons-quantity__counter  quantity"
                                 data-quantity
                                 data-quantity-max="4">
                               <button type="button" class="quantity__btn  _minus  btn" data-quantity-minus>-
                               </button>
                               <input type="text" class="quantity__value" value="0"
                                      data-children
                                      readonly
                                      data-quantity-value>
                               <button type="button" class="quantity__btn  _plus  btn" data-quantity-plus>+
                               </button>
                            </div>
                         </div>
                      </div>
                   </div>

             </div>
             <div class="persons-quantity__footer">
                <button type="button" class="persons-quantity__add-item  btn" data-booking-add-room>
                   + <?= Yii::t('hotel', 'Номер') ?>
                </button>
                <button type="button"
                        class="add-room-to-cart persons-quantity__success  btn"
                        data-booking-to-cart>
                   <img class="__invisible" width="27" src="<?php echo Url::to('img/gif/preloader.gif', true) ?>"/>
                   <svg class="__visible">
                      <use xlink:href="<?= Url::to('./img/svg/sprite-main.svg#ico-cart') ?>"></use>
                   </svg>
                </button>
             </div>
          </div>
          <p class="room-booking__no-rooms"
             style="display: none"><?= Yii::t('hotel', 'На даний період вільні номери відсутні') ?></p>
       </div>
    <?php endif; ?>
</div>


