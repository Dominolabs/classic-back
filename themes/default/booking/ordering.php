<?php

/* @var $page array */

use app\components\CorrectSpelling;
use app\components\UrlHelper;
use yii\helpers\Url;

/* @var $default_page array */
/* @var $this yii\web\View */
/* @var $rooms array */
/* @var $booking_period array */
/* @var $countries array */



$this->title = $page['meta_title'] ? $page['meta_title'] :
    ($default_page['meta_title'] ? $default_page['meta_title'] : ($page['title'] ? $page['title'] : $default_page['title']));

$meta_description = $page['meta_description'] ? $page['meta_description'] : $default_page['meta_description'];
$meta_keywords = $page['meta_keyword'] ? $page['meta_keyword'] : $default_page['meta_keyword'];

$this->params['metaDescription'] = $meta_description;
$this->params['metaKeywords'] = $meta_keywords;
$vocabulary = new CorrectSpelling();
$rooms_quantity = $cart->getRoomsQuantity();
$guests_quantity = $cart->getGuestsQuantity();
$total_value = $cart->getTotalValue();
$cart_content = $cart->addRoomsDescription()->getContent();
//dd(Yii::$app->formatter->asDate($booking_period['to'], 'YYYY-MM-dd'));
?>
<div class="page-content__content  section-page">
   <div id="booking-ordering-form" class="section-page__ordering ordering">
      <h1 class="ordering__title"><?php Yii::t('hotel', 'Оформлення бронювання') ?></h1>

      <div class="ordering__details _slide _inline _closed" data-toggle="can-click-out">
         <div class="ordering__details-main">
            <div class="ordering__details-main-row">
               <div class="ordering__details-info"><?php echo Yii::$app->formatter->asDate($booking_period['from'], 'dd MMMM'); ?> - <?php echo Yii::$app->formatter->asDate($booking_period['to'], 'dd MMMM'); ?>,
                  <?php echo $rooms_quantity . ' ' . $vocabulary->get('номер', $rooms_quantity) ?>,
                  <?php echo $guests_quantity . ' ' . $vocabulary->get('гість', $guests_quantity) ?>
               </div>
               <div class="ordering__details-price">Загальна вартість: <b><?php echo number_format($total_value, 0, '.', ' ') ?> ₴</b></div>
            </div>
            <button type="button" class="ordering__details-more"
                    data-toggle-trigger
            >
               <span class="text-closed"><?php echo Yii::t('hotel', 'Детальніше') ?></span>
               <span class="text-open"><?php echo Yii::t('hotel', 'Згорнути') ?></span>
               <svg>
                  <use xlink:href="<?php echo UrlHelper::toAbsolute('img/svg/sprite-main.svg#ico-arrow-down') ?>"></use>
               </svg>
            </button>
         </div>

         <div class="ordering__details-toggle" data-toggle-block>
            <div class="ordering__details-toggle-inner">
               <?php foreach($cart_content as $position):  ?>
               <div class="ordering__details-room">
                  <div class="ordering__details-room-head">
                     <h3 class="ordering__details-room-name"><?php echo $position['info']['name'] ?></h3>
                     <div class="ordering__details-room-price"><?php echo number_format(($position['pricePerDay'] * $position['days']), 0, '.', ' ') ?> ₴</div>
                  </div>
                  <div class="ordering__details-room-persons"><?php echo $position['adult'] . ' ' . $vocabulary->get('дорослий', $position['adult']) ?>, <?php echo $position['children'] . ' ' . $vocabulary->get('дитина', $position['children']) ?></div>
               </div>
               <?php endforeach; ?>
            </div>
         </div>
      </div>

      <h5 id="internal-error-note" style="color: red; font-size: 40px; margin: 20px 0"></h5>

      <div class="ordering__info">
         <h2 class="ordering__info-title"><?php echo Yii::t('hotel', 'Інформація про замовника') ?></h2>
         <div class="ordering__info-form">
            <div class="ordering__info-form-inner">
               <label id="label-lastname" class="input">
                  <span class="input__title"><?php echo Yii::t('hotel', 'Прізвище') ?></span>
                  <span class="input__inner">
                     <input name="lastname" class="input__input" type="text">
                  </span>
                  <span class="input__error"></span>
               </label>
               <label id="label-name" class="input">
                  <span class="input__title"><?php echo Yii::t('hotel', 'Ім\'я') ?></span>
                  <span class="input__inner">
                     <input name="name" class="input__input" type="text">
                  </span>
                  <span class="input__error"></span>
               </label>
               <label id="label-surname" class="input">
                  <span class="input__title"><?php echo Yii::t('hotel', 'По батькові') ?></span>
                  <span class="input__inner">
                     <input name="surname" class="input__input" type="text">
                  </span>
                  <span class="input__error"></span>
               </label>
               <label id="label-birth_date" class="input">
                  <span class="input__title"><?php echo Yii::t('hotel', 'Дата народження') ?></span>
                  <span class="input__inner">
                     <input class="input__input" type="text"
                            name="birth_date"
                            data-birthday
                            data-language="<?php echo Yii::$app->language ?>">
                  </span>
                  <span class="input__error"></span>
               </label>
               <label id="label-country_id" class="input">
                  <span class="input__title"><?php echo Yii::t('hotel', 'Громадянство') ?></span>
                  <span class="input__inner">
                     <select data-select-custom name="country_id" style="width: 100%">
                        <?php if(isset($countries) and !empty($countries)): ?>
                           <?php foreach($countries as $country): ?>
                              <option value="<?php echo $country['country_id'] ?>" <?php echo (strtolower($country['name']) === 'ukraine') ? 'selected' : '' ?>><?php echo $country['name'] ?></option>
                           <?php endforeach; ?>
                        <?php endif; ?>
                     </select>
                  </span>
                  <span class="input__error"></span>
               </label>
               <label id="label-phone" class="input">
                  <span class="input__title"><?php echo Yii::t('hotel', 'Телефон') ?></span>
                  <span class="input__inner">
                     <span class="input__tel-codes">
                        <select data-select-tel-code name="phone_code_id" style="width: 100%">
                           <?php if(isset($countries) and !empty($countries)): ?>
                              <?php foreach($countries as $country): ?>
                                 <option value="<?php echo $country['phone_code'] ?>" <?php echo (strtolower($country['name']) === 'ukraine') ? 'selected' : '' ?>><?php echo $country['name'] ?> (<?php echo $country['phone_code'] ?>)</option>
                              <?php endforeach; ?>
                           <?php endif; ?>
                        </select>
                     </span>
                     <input name="phone" class="input__input" type="text">
                  </span>
                  <span class="input__error"></span>
               </label>
               <label id="label-email" class="input">
                  <span class="input__title"><?php echo Yii::t('hotel', 'Електронна пошта') ?></span>
                  <span class="input__inner">
                     <input name="email" class="input__input" type="text">
                  </span>
                  <span class="input__error"></span>
               </label>
            </div>

         </div>
      </div>
      <div class="ordering__info">
         <h2 class="ordering__info-title"><?php echo Yii::t('hotel', 'Додаткова інформація') ?></h2>
         <div class="ordering__info-form">
            <div class="ordering__info-form-inner" data-booking>
               <label id="label-checkin_at" class="input">
                  <span class="input__title"><?php echo Yii::t('hotel', 'Заїзд') ?></span>
                  <span class="input__inner">
                     <input class="input__input" type="text"
                            name="checkin_at" value="<?php echo Yii::$app->formatter->asDate($booking_period['from'], 'dd/MM/YYYY'); ?>" disabled>
                  </span>
                  <span class="input__error"></span>
               </label>
               <label id="label-departure_at" class="input">
                  <span class="input__title"><?php echo Yii::t('hotel', 'Виїзд') ?></span>
                  <span class="input__inner">
                     <input class="input__input" type="text"
                            name="departure_at" value="<?php echo Yii::$app->formatter->asDate($booking_period['to'], 'dd/MM/YYYY'); ?>" disabled >
                  </span>
                  <span class="input__error"></span>
               </label>
               <label id="label-payment_type" class="input">
                  <span class="input__title"><?php Yii::t('hotel', 'Спосіб оплати') ?></span>
                  <span class="input__inner">
                     <select data-select-custom name="payment_type" style="width: 100%">
                        <option value="1" selected><?php echo Yii::t('hotel', 'Готівкою') ?></option>
                        <option value="2"><?php echo Yii::t('hotel', 'Онлайн') ?></option>
                     </select>
                  </span>
               </label>
               <label id="label-comment" class="input _full">
                  <span class="input__title"><?php echo Yii::t('hotel', 'Коментар') ?></span>
                  <span class="input__inner">
                     <textarea name="comment" class="input__input" rows="3"></textarea>
                  </span>
                  <span class="input__error"></span>
               </label>
            </div>
         </div>
      </div>

      <div class="ordering__footer">
         <div class="ordering__footer-text"><?php echo Yii::t('hotel', 'Фактом бронювання Ви погоджуєтесь з') ?>
            <a href="<?php echo Url::to('politika-konfidentsiynosti', true) ?>"><?php echo Yii::t('hotel', 'обробкою персональних даних та політикою конфіденційності') ?></a>
            <?php echo Yii::t('hotel', 'та') ?> <a href="<?php echo Url::to('dogovir-publichno-oferti', true) ?>"><?php echo Yii::t('hotel', 'угодою користувача') ?></a>
         </div>
         <div class="ordering__btn-wrap">
            <button id="create-order-btn" type="submit" class="ordering__btn-submit  btn">
               <span class="__visible"><?php echo Yii::t('hotel', 'Забронювати') ?></span>
               <img class="__invisible" width="27" src="<?php echo Url::to('img/gif/preloader.gif', true) ?>" />
            </button>
         </div>
      </div>
   </div>
</div>
