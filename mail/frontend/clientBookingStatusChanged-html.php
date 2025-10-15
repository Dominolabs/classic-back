<?php

use app\module\admin\module\booking\models\Booking;
use app\module\admin\module\reservation\models\Reservation;

/* @var $this yii\web\View */
/* @var $booking Reservation */
/* @var $old_status */
/* @var $new_status */

$cart = json_decode($booking->cart, true);
?>
<div class="password-reset">
    <p><b>Изменение статуса заявки на бронирование номера.</b></p>
    <p>Статус Вашей заявки на бронирования номера № <?= $booking->booking_id ?> от <?= Yii::$app->formatter->asDate($booking->created_at, 'php:d.m.Y H:i') ?> изменен с "<?= Booking::getStatusName($old_status) ?>" на "<?= Booking::getStatusName($new_status) ?>"</p>
    <br/>
    <p>Информация о заказе:</p>
    <p><b>№ заявки:</b> <?= $booking->booking_id ?></p>
    <p><b>Имя:</b> <?= $booking->name . ' ' . $booking->lastname ?></p>
    <p><b>Заказ:</b></p>
    <?php foreach ($cart as $position): ?>
    <p>Номер:</b> <?php echo  $position['room_name']; ?></p>
    <p>Гостей:</b> Взрослых - <?php echo $position['guests']['adults']['quantity']; ?>, Детей - <?php echo  $position['guests']['children']['quantity']; ?></p>
    <?php endforeach; ?>
    <p><b>Дата заселения:</b> <?= Yii::$app->formatter->asDate($booking->checkin_at, 'php:d.m.Y') ?></p>
    <p><b>Дата выселения:</b> <?= Yii::$app->formatter->asDate($booking->departure_at, 'php:d.m.Y') ?></p>
    <p><b>Статус:</b> "<?= $booking->getStatusName($booking->status) ?>"</p>
    <p><b>Создано:</b> <?= Yii::$app->formatter->asDate($booking->created_at, 'php:d.m.Y H:i') ?></p>
</div>
