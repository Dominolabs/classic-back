<?php

use app\module\admin\module\order\models\Order;

/* @var $this yii\web\View */
/* @var $order Order */
/* @var $orderProducts array */
?><?= $this->render('_order-text', compact('order', 'orderProducts')) ?>
