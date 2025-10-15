<?php

use app\module\admin\module\reservation\models\Reservation;

/* @var $this yii\web\View */
/* @var $booking Reservation */
/* @var $old_status */
/* @var $new_status */
$cart = json_decode($booking->cart, true);
?>
Изменение статуса заявки на бронирование номера.

Статус Вашей заявки на бронирования номера № <?= $booking->booking_id ?> от <?= Yii::$app->formatter->asDate($booking->created_at, 'php:d.m.Y H:i') ?> изменен с <?= $old_status ?> на <?= $new_status ?>: <?= $booking->booking_id . PHP_EOL ?>

Информация о заказе:
№ заявки: <?= $booking->booking_id . PHP_EOL ?>
Имя: <?= $booking->name . ' ' . $booking->lastname . PHP_EOL ?>
Заказ:
<?php foreach ($cart as $position): ?>
   Номер: <?php echo  $position['room_name'] . PHP_EOL; ?>
   Гостей: Взрослых - <?php echo $position['guests']['adults']['quantity']; ?>, Детей - <?php echo  $position['guests']['children']['quantity'] . PHP_EOL; ?>
<?php endforeach; ?>
Дата заселения: <?= Yii::$app->formatter->asDate($booking->checkin_at, 'php:d.m.Y') . PHP_EOL ?>
Дата выселения: <?= Yii::$app->formatter->asDate($booking->departure_at, 'php:d.m.Y') . PHP_EOL ?>
Статус: <?= $booking->getStatusName($booking->status) . PHP_EOL ?>
Создано: <?= Yii::$app->formatter->asDate($booking->created_at, 'php:d.m.Y H:i') . PHP_EOL ?>
