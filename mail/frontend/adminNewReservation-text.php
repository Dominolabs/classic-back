<?php

use app\module\admin\module\reservation\models\Reservation;

/* @var $this yii\web\View */
/* @var $reservation Reservation */
/* @var $orderProducts array */
?>
Вы получили новую заявку на бронирование номера.

№ заявки: <?= $reservation->reservation_id . PHP_EOL ?>

Имя: <?= $reservation->name . PHP_EOL ?>
Номер телефона: <?= $reservation->phone . PHP_EOL ?>
Тип номера: <?= $reservation->roomType->roomTypeName; ?>
Дата заселения: <?= Yii::$app->formatter->asDate($reservation->arrived_at, 'php:d.m.Y') . PHP_EOL ?>
Дата выселения: <?= Yii::$app->formatter->asDate($reservation->evictioned_at, 'php:d.m.Y') . PHP_EOL ?>
Комементарий: <?= $reservation->comment . PHP_EOL ?>
Статус: <?= Reservation::getStatusName($reservation->status) . PHP_EOL ?>
Создано: <?= Yii::$app->formatter->asDate($reservation->created_at, 'php:d.m.Y H:i') . PHP_EOL ?>
