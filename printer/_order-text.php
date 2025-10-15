<?php

use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\order\models\Order;
use app\module\admin\module\order\models\OrderProduct;
use app\module\admin\module\product\models\Product;

/* @var $order Order */
/* @var $orderProducts array */
?>
<?= PHP_EOL . PHP_EOL . PHP_EOL ?>
№ замовлення: <?= $order->order_id . PHP_EOL ?>
Дата замовлення: <?= Yii::$app->formatter->asDate($order->created_at, 'php:d.m.Y H:i') . PHP_EOL ?>
Статус замовлення: <?= Order::getStatusName($order->status) . PHP_EOL ?>
Клієнт: <?= $order->name . PHP_EOL ?>
Email: <?= $order->email . PHP_EOL ?>
Номер телефону: <?= $order->phone . PHP_EOL ?>
Спосіб оплати: <?= Order::getPaymentTypeName((int)$order->payment_type) . PHP_EOL ?>
Статус оплати: <?= Order::getPaymentStatusName((int)$order->payment_status) . PHP_EOL ?>
Населений пункт: <?= $order->getCityName() . PHP_EOL ?>
Вулиця: <?= $order->street . PHP_EOL ?>
Під'їзд: <?= $order->entrance . PHP_EOL ?>
№ будинку: <?= $order->house_number . PHP_EOL ?>
№ квартири: <?= $order->apartment_number . PHP_EOL ?>
<?php if ($order->call_me_back == 1): ?>
Зателефонувати клієнту
<?php endif; ?>
Час доставки: <?= Yii::$app->formatter->asDate($order->time, 'php:d.m.Y H:i') . PHP_EOL ?>

==========================================
Товари:
==========================================
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

    $productName = $orderProduct->name . ' ' . $orderProduct->getProductTypeString() . ' / ' . Currency::format($productPrice, 'UAH', 1);

    $result = $orderProduct->quantity . 'x ' . $productName . ' / Всего: ' . Product::formatPrice($orderProduct->total, 'UAH');

    if (!empty($mainIngredients) || !empty($additionalIngredients)) {
        $result .= PHP_EOL . PHP_EOL;
        if (!empty($mainIngredients)) {
            $result .= 'Основні інгредієнти' . PHP_EOL;
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
            $result .= 'Додаткові інгредієнти' . PHP_EOL;
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
        $result .= 'Коментар до товару:' . PHP_EOL;
        $result .= $orderProduct->comment . PHP_EOL;
        $result .= PHP_EOL;
    }
    $result .= '------------------------------------------' . PHP_EOL;
    ?>
    <?= $result ?>
<?php endforeach; ?>
<?php if (!empty($order->comment)): ?>
Побажання клієнта до замовлення: <?= $order->comment . PHP_EOL ?>
<?php endif; ?>
<?php if ($order->getCityName() === null): ?>
Доставка: <?= Product::formatPrice($order->delivery, 'UAH') . PHP_EOL ?>
<?php else: ?>
Доставка (<?= $order->getCityName() ?>): <?= Product::formatPrice($order->delivery, 'UAH') . PHP_EOL ?>
<?php endif; ?>
Упаковка: <?= Product::formatPrice($order->packing, 'UAH') . PHP_EOL ?>
Сума: <?= Product::formatPrice($order->sum, 'UAH') . PHP_EOL ?>
==========================================
Всього: <?= Product::formatPrice($order->total, 'UAH') . PHP_EOL ?>
==========================================
<?= PHP_EOL . PHP_EOL ?>
