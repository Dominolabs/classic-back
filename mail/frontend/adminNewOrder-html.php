<?php

use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\order\models\Order;
use app\module\admin\module\order\models\OrderProduct;
use app\module\admin\module\product\models\Product;

/* @var $this yii\web\View */
/* @var $order Order */
/* @var $orderProducts array */
?>
<div class="password-reset">
    <p>Вы получили новый заказ.</p>
    <p>№ заказа: <?= $order->order_id ?></p>
    <p>Дата заказа: <?= Yii::$app->formatter->asDate($order->created_at, 'php:d.m.Y H:i') ?></p>
    <p>Статус заказа: <?= Order::getStatusName($order->status) ?></p>
    <p>Имя клиента: <?= $order->name ?></p>
    <p>Email: <?= $order->email ?></p>
    <p>Номер телефона: <?= $order->phone ?></p>
    <p>Способ оплаты: <?= Order::getPaymentTypeName($order->payment_type) ?></p>
    <p>Статус оплаты: <?= Order::getPaymentStatusName((int)$order->payment_status) ?></p>
    <p>Населенный пункт: <?= $order->getCityName() ?></p>
    <p>Улица: <?= $order->street ?></p>
    <p>Подъезд: <?= $order->entrance ?></p>
    <p>№ квартиры: <?= $order->house_number ?></p>
    <p>№ дома: <?= $order->apartment_number ?></p>
    <p>Время доставки: <?= Yii::$app->formatter->asDate($order->time, 'php:d.m.Y H:i') ?></p>
    <p>Товары:</p>
    <ul>
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
                $result .= '<br/></br>';
                if (!empty($mainIngredients)) {
                    $result .= '<p>Основные ингредиенты:</p>';
                    $result .= '<ul>';
                    foreach ($mainIngredients as $mainIngredient) {
                        if ($orderProduct->type === 'classic') {
                            $price = 0.0000;
                        } else {
                            $price = $mainIngredient['price'];
                        }
                        $result .= '<li>' . $mainIngredient['name'] . ' / ' . Currency::format($price, 'UAH', 1) . ' / ' . ' x ' . $mainIngredient['quantity'] . '</li>';
                    }
                    $result .= '</ul>';
                }
                if (!empty($additionalIngredients)) {
                    $result .= '<p>Дополнительные ингредиенты:</p>';
                    $result .= '<ul>';
                    foreach ($additionalIngredients as $additionalIngredient) {
                        $price = $additionalIngredient['price'];
                        $result .= '<li>' . $additionalIngredient['name'] . ' / ' . Currency::format($price, 'UAH', 1) . ' / ' . ' x ' . $additionalIngredient['quantity'] . '</li>';
                    }
                    $result .= '</ul>';
                }
            }

            if (!empty($orderProduct->properties) && $arr = json_decode($orderProduct->properties, true)) {
                $result .= '<br><p>Соус: ' . ($arr['property']['uk'] ?? '') . '</p>>';
            }

            if (!empty($orderProduct->comment)) {
                $result .= '<br><br><p style="font-size:12px; margin: 0; padding: 0"><i><b>Комментарий к товару</b></i></p>';
                $result .= "<span style=\"font-size:12px\">$orderProduct->comment</span>";
            }
            ?>
            <li><?= $result ?></li>
        <?php endforeach; ?>
    </ul>

    <?php if ($order->getCityName() === null): ?>
        <p>Доставка: <?= Product::formatPrice($order->delivery, 'UAH') ?></p>
    <?php else: ?>
        <p>Доставка (<?= $order->getCityName() ?>): <?= Product::formatPrice($order->delivery, 'UAH') ?></p>
    <?php endif; ?>
    <p>Упаковка: <?= Product::formatPrice($order->packing, 'UAH') ?></p>
    <p>Сумма: <?= Product::formatPrice($order->sum, 'UAH') ?></p>
    <p>Итого: <?= Product::formatPrice($order->total, 'UAH') ?></p>
    <?php if (!empty($order->comment)): ?>
        <p>Пожелания клиента к заказу: <?= $order->comment ?></p>
    <?php endif; ?>
    <?php if ((int)$order->do_not_call === Order::YES): ?>
        <p>Не звонить клиенту в дверь</p>
    <?php endif; ?>
    <?php if ((int)$order->have_a_child === Order::YES): ?>
        <p>У клиента есть маленький ребенок</p>
    <?php endif; ?>
    <?php if ((int)$order->have_a_dog === Order::YES): ?>
        <p>У клиента есть собака</p>
    <?php endif; ?>
</div>
