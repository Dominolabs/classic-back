<?php
use yii\helpers\Url;
/* @var $this yii\web\View */
?>
<div class="modal  modal-alert" data-modal="alert">
   <div class="modal__content">
      <a class="modal__close  js-modal-close">
         <svg width="26px" height="26px">
            <use xlink:href="<?php echo Url::to('./img/svg/sprite-main.svg#ico-close') ?>"></use>
         </svg>
      </a>
      <div class="modal-alert__main-content">
         <h2 class="modal-alert__title">Дякуємо!</h2>
         <div class="modal-alert__text text">
            <p><?php echo Yii::t('hotel', 'Ваше замовлення отримано. Підтвердження надійде Вам на електронну пошту') ?></p>
         </div>
         <div class="modal-alert__btn-wrap">
            <a href="#" class="btn js-modal-close">Ок</a>
         </div>
      </div>
   </div>
</div>
