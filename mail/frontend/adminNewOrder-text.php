<?php

use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\order\models\Order;
use app\module\admin\module\order\models\OrderProduct;
use app\module\admin\module\product\models\Product;

/* @var $this yii\web\View */
/* @var $order Order */
/* @var $orderProducts array */
?>
Вы получили новый заказ.

№ заказа: <?= $order->order_id . PHP_EOL ?>

Дата заказа: <?= Yii::$app->formatter->asDate($order->created_at, 'php:d.m.Y H:i') ?>

Статус заказа: <?= Order::getStatusName($order->status) ?>

Имя клиента: <?= $order->name ?>

Email: <?= $order->email ?>

Номер телефона: <?= $order->phone ?>

Способ оплаты: <?= Order::getPaymentTypeName($order->payment_type) ?>

Статус оплаты: <?= Order::getPaymentStatusName((int)$order->payment_status) ?>

Населенный пункт: <?= $order->getCityName() ?>

Улица: <?= $order->street ?>

Подъезд: <?= $order->entrance ?>

№ квартиры: <?= $order->house_number ?>

№ дома: <?= $order->apartment_number ?>

Время доставки: <?= Yii::$app->formatter->asDate($order->time, 'php:d.m.Y H:i') ?>

Товары:

<?php /** @var $orderProduct OrderProduct */ ?>
<?php foreach ($orderProducts as $orderProduct): ?>
    <?php
    $ingredients = json_decode($orderProduct->ingredients, true);
    $mainIngredients = [];
    $additionalIngredients = [];

    $productPrice = 0;

    if (!empty($ingredients['main_ingredients'])) {
        $mainIngredients = $ingredients['main_ingredients'];
        foreach ($mainIngredients as $ingredient) {
            if ($orderProduct->type !== 'classic') {
                $productPrice += $ingredient['price'];
            }
        }
    }

    if (!empty($ingredients['additional_ingredients'])) {
        $additionalIngredients = $ingredients['additional_ingredients'];
        foreach ($additionalIngredients as $ingredient) {
            $productPrice += $ingredient['price'];
        }
    }
    $productPrice = $orderProduct->price - $productPrice;

    $productName = $orderProduct->name . ' ' . $orderProduct->getProductTypeString() . ' / ' . Currency::format($orderProduct->price, 'UAH', 1);

    $result = $orderProduct->quantity . 'x ' . $productName . ' / Всего: ' . Product::formatPrice($orderProduct->total, 'UAH');

    if (!empty($mainIngredients) || !empty($additionalIngredients)) {
        $result .= PHP_EOL . PHP_EOL;
        if (!empty($mainIngredients)) {
            $result .= 'Основные ингредиенты:';
            $result .= PHP_EOL;
            foreach ($mainIngredients as $mainIngredient) {
                if ($orderProduct->type === 'classic') {
                    $price = 0.0000;
                } else {
                    $price = $mainIngredient['price'];
                }
                $result .= $mainIngredient['name'] . ' / ' . Currency::format($price, 'UAH', 1) . ' / ' . ' x ' . $mainIngredient['quantity'] . PHP_EOL;
            }
            $result .= PHP_EOL;
        }
        if (!empty($additionalIngredients)) {
            $result .= 'Дополнительные ингредиенты:';
            $result .= PHP_EOL;
            foreach ($additionalIngredients as $additionalIngredient) {
                $price = $additionalIngredient['price'];
                $result .= $additionalIngredient['name'] . ' / ' . Currency::format($price, 'UAH', 1) . ' / ' . ' x ' . $additionalIngredient['quantity'] . PHP_EOL;
            }
            $result .= PHP_EOL;
        }
    }
    if (!empty($orderProduct->properties) && $arr = json_decode($orderProduct->properties, true)) {
        $result .= 'Соус: ' . ($arr['property']['uk'] ?? '') . PHP_EOL;
        $result .= PHP_EOL;
    }

    if (!empty($orderProduct->comment)) {
        $result .= PHP_EOL . PHP_EOL . 'Комментарий к товару' . PHP_EOL;
        $result .= $orderProduct->comment;
    }
    ?>
    <li><?= $result ?></li>
<?php endforeach; ?>

<?php if ($order->getCityName() === null): ?>
    Доставка: <?= Product::formatPrice($order->delivery, 'UAH') ?>
<?php else: ?>
    Доставка (<?= $order->getCityName() ?>): <?= Product::formatPrice($order->delivery, 'UAH') ?>
<?php endif; ?>

Упаковка: <?= Product::formatPrice($order->packing, 'UAH') ?>

Сумма: <?= Product::formatPrice($order->sum, 'UAH') ?>

Итого: <?= Product::formatPrice($order->total, 'UAH') ?>

<?php if (!empty($order->comment)): ?>
    Пожелания клиента к заказу: <?= $order->comment ?>
<?php endif; ?>
<?php if ((int)$order->do_not_call === Order::YES): ?>
    Не звонить клиенту в дверь
<?php endif; ?>
<?php if ((int)$order->have_a_child === Order::YES): ?>
    У клиента есть маленький ребенок
<?php endif; ?>
<?php if ((int)$order->have_a_dog === Order::YES): ?>
    У клиента есть собака
<?php endif; ?>
