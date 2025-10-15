<?php

use app\components\cart\BookingCart;
use app\widgets\BookingForm;
use app\components\CorrectSpelling;
use yii\helpers\Url;

/* @var $page array */
/* @var $defaultPage array */
/* @var $this yii\web\View */
/* @var $rooms array */
/* @var $from_time */
/* @var $to_time */
/* @var $nights_quantity */
/* @var $vocabulary */


$this->title = $page['meta_title'] ? $page['meta_title'] :
    ($defaultPage['meta_title'] ? $defaultPage['meta_title'] : ($page['title'] ? $page['title'] : $defaultPage['title']));

$metaDescription = $page['meta_description'] ? $page['meta_description'] : $defaultPage['meta_description'];
$metaKeywords = $page['meta_keyword'] ? $page['meta_keyword'] : $defaultPage['meta_keyword'];

$this->params['metaDescription'] = $metaDescription;
$this->params['metaKeywords'] = $metaKeywords;
$cart = new BookingCart();
$cart_info = [
    'rooms' => $cart->getRoomsQuantity(),
    'guests' => $cart->getGuestsQuantity(),
    'total_value' => $cart->getTotalValue()
];
$vocabulary = new CorrectSpelling();
$rooms_selected = $cart->getRoomsAndQuantity();

?>
<div class="page-content__content  section-page">
   <div class="section-page__booking-header booking-header">
      <div class="booking-header__title-wrap">
         <h1 class="booking-header__title"><?= $this->title ?></h1>
      </div>
      <form class="booking-header__form booking-form" data-booking>
          <?= BookingForm::widget() ?>
      </form>
   </div>
   <div class="section-page__booking-list booking-list">
      <div class="booking-list__wrapper">
          <?php if (!empty($rooms)) : ?>
              <?php foreach ($rooms as $key => $room): ?>
                  <?= $this->render('_room', compact('room', 'key', 'nights_quantity', 'vocabulary', 'rooms_selected', 'cart')) ?>
              <?php endforeach; ?>
          <?php else: ?>
             <p><?= Yii::t('hotel', 'Нажаль вільних номерів не знайдено.') ?></p>
          <?php endif; ?>
      </div>
   </div>

   <form class="booking-fixed-cart" id="booking-fixed-cart">
      <div class="booking-fixed-cart__content">
         <div class="booking-fixed-cart__ico">
            <svg>
               <use xlink:href="./img/svg/sprite-main.svg#ico-cart"></use>
            </svg>
         </div>
         <div class="booking-fixed-cart__info-wrap  g-mob-x-hide">
            <p class="booking-fixed-cart__rooms-wrap">
               <span id="cart-rooms-quantity-span" data-rooms-quantity><?php echo $cart_info['rooms'] ?></span>
               <span id="cart-rooms-quantity-noun-span"> <?php echo $vocabulary->get('номер', $cart_info['rooms']) ?></span>
               / <span id="cart-rooms-quests-span"  data-persons-quantity><?php echo $cart_info['guests'] ?></span>
               <span id="cart-rooms-quests-noun-span"><?php echo $vocabulary->get('гість', $cart_info['guests']) ?></span>
            </p>
            <p class="booking-fixed-cart__dates"><span class="g-mob-hide"><?php echo Yii::$app->formatter->asDate($from_time, 'dd MMMM'); ?> - <?php echo Yii::$app->formatter->asDate($to_time, 'dd MMMM'); ?>
               </span>(<span id="cart-nights-quantity-span"><?php echo $nights_quantity ?></span> <span id="cart-nights-quantity-noun-span"><?= $vocabulary->get('ніч', $nights_quantity) ?></span>)
            </p>
         </div>
         <p class="booking-fixed-cart__price"><span data-price><?php echo number_format($cart_info['total_value'], 0, '.', ' ') ?></span> ₴</p>
         <a id="add-booking-to-cart" href="<?php echo Url::to(['order'], true) ?>" class="booking-fixed-cart__booking-btn  btn"><?php echo Yii::t('hotel', 'Забронювати') ?></a>
         <button id="clear-booking-cart" style="margin-left: 20px; background-color: #0b3e6f" class="booking-fixed-cart__booking-btn  btn"><?php echo Yii::t('hotel', 'Очистити') ?></button>
         <input name="cart" type="hidden" value='<?php echo $cart->getContentJson() ?>' id="booking-cart-value">
      </div>
   </form>
</div>
