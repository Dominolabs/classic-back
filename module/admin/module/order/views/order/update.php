<?php
/**
 * Order update view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\order\models\Order */
/* @var $orderProductDataProvider yii\data\ActiveDataProvider */
/* @var $orderHistoryModel app\module\admin\module\order\models\OrderHistory */
/* @var $orderHistoryDataProvider yii\data\ActiveDataProvider */
/* @var $lastOrderHistory app\module\admin\module\order\models\OrderHistory */

$this->title = 'Изменить заказ № ' . $model->order_id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить заказ № ' . $model->order_id;
?>
<div class="order-update">
    <?= $this->render('_form', [
        'model' => $model,
        'orderProductDataProvider' => $orderProductDataProvider,
        'orderHistoryModel' => $orderHistoryModel,
        'orderHistoryDataProvider' => $orderHistoryDataProvider,
        'lastOrderHistory' => $lastOrderHistory
    ]) ?>
</div>
