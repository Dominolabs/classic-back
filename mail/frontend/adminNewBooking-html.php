<?php

use app\module\admin\module\reservation\models\Reservation;

/* @var $this yii\web\View */
/* @var $booking Reservation */
/* @var $country */

$cart = json_decode($booking->cart, true);
?>
<div class="password-reset">
    <p><b>Новая заявка на бронирование номера.</b></p>
    <p><b>№ заявки:</b> <?= $booking->booking_id ?></p>
    <p><b>Имя:</b> <?= $booking->name . ' ' . $booking->lastname ?></p>
    <p><b>Номер телефона:</b> <?= $country ? $country->phone_code . $booking->phone : '' ?></p>
    <p><b>Заказ:</b></p>
    <?php foreach ($cart as $position): ?>
    <p>Номер:</b> <?php echo  $position['room_name']; ?></p>
    <p>Гостей:</b> Взрослых - <?php echo $position['guests']['adults']['quantity']; ?>, Детей - <?php echo  $position['guests']['children']['quantity']; ?></p>
    <?php endforeach; ?>
    <p><b>Дата заселения:</b> <?= Yii::$app->formatter->asDate($booking->checkin_at, 'php:d.m.Y') ?></p>
    <p><b>Дата выселения:</b> <?= Yii::$app->formatter->asDate($booking->departure_at, 'php:d.m.Y') ?></p>
    <p><b>Статус:</b> <?= $booking->getStatusName($booking->status) ?></p>
    <p><b>Создано:</b> <?= Yii::$app->formatter->asDate($booking->created_at, 'php:d.m.Y H:i') ?></p>
</div>
