<?php

use app\module\admin\module\reservation\models\Reservation;

/* @var $this yii\web\View */
/* @var $booking Reservation */
/* @var $country */
$cart = json_decode($booking->cart, true);
?>
Вы оформили новую заявку на бронирование номера.

№ заявки: <?= $booking->booking_id . PHP_EOL ?>

Имя: <?= $booking->name . ' ' . $booking->lastname . PHP_EOL ?>
Номер телефона: <?= $country ? $country->phone_code . $booking->phone : '' . PHP_EOL ?>
Заказ:
<?php foreach ($cart as $position): ?>
   Номер: <?php echo  $position['room_name'] . PHP_EOL; ?>
   Гостей: Взрослых - <?php echo $position['guests']['adults']['quantity']; ?>, Детей - <?php echo  $position['guests']['children']['quantity'] . PHP_EOL; ?>
<?php endforeach; ?>
Дата заселения: <?= Yii::$app->formatter->asDate($booking->checkin_at, 'php:d.m.Y') . PHP_EOL ?>
Дата выселения: <?= Yii::$app->formatter->asDate($booking->departure_at, 'php:d.m.Y') . PHP_EOL ?>
Статус: <?= $booking->getStatusName($booking->status) . PHP_EOL ?>
Создано: <?= Yii::$app->formatter->asDate($booking->created_at, 'php:d.m.Y H:i') . PHP_EOL ?>
