<?php

use app\module\admin\module\reservation\models\Reservation;

/* @var $this yii\web\View */
/* @var $reservation Reservation */
/* @var $orderProducts array */
?>
<div class="password-reset">
    <p>Вы получили новую заявку на бронирование номера.</p>
    <p>№ заявки: <?= $reservation->reservation_id ?></p>
    <p>Имя: <?= $reservation->name ?></p>
    <p>Номер телефона: <?= $reservation->phone ?></p>
    <p>Тип номера: <?= $reservation->roomType->roomTypeName; ?></p>
    <p>Дата заселения: <?= Yii::$app->formatter->asDate($reservation->arrived_at, 'php:d.m.Y') ?></p>
    <p>Дата выселения: <?= Yii::$app->formatter->asDate($reservation->evictioned_at, 'php:d.m.Y') ?></p>
    <p>Комементарий: <?= $reservation->comment ?></p>
    <p>Статус: <?= Reservation::getStatusName($reservation->status) ?></p>
    <p>Создано: <?= Yii::$app->formatter->asDate($reservation->created_at, 'php:d.m.Y H:i') ?></p>
</div>
