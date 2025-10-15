<?php

use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\order\models\Order;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;
use kartik\rating\StarRating;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\module\order\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $deleted_only */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index box box-primary">
    <div class="box-body table-responsive">

        <?= Html::a('<i class="fa fa-minus-circle"></i> Очистить фильтр', Url::to('/admin/order/order'), ['class' => 'btn btn-primary btn-delete-badge', 'style' => ['margin-bottom' => '20px']]) ?>
        <?= Html::a('<i class="fa fa-trash"></i> Удаленные', Url::to('/admin/order/order?is_deleted=true'), ['class' => 'btn btn-danger btn-delete-badge', 'style' => ['margin-bottom' => '20px']]) ?>
        <?php Pjax::begin(['id' => 'order-pjax-container']) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'rowOptions' => function($model, $key, $index, $column) {
                if ($model->status == Order::STATUS_PENDING) {
                    return ['class' => 'danger'];
                }
            },
            'columns' => [

                'order_id',
                [
                    'attribute' => 'name',
                    'label' => 'Клиент',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $url = "<a href=\""
                            . Url::to('/admin/order/order?OrderSearch[name]=' . $model->name)
                            . "\">$model->name</a> ";
                        return $url;
                    }
                ],[
                    'attribute' => 'payment_type',
                    'filter' => Order::getPaymentTypesList(),
                    'value' => function ($model) {
                        return  Order::getPaymentTypeName($model->payment_type);
                    }
                ],
                [
                    'attribute' => 'delivery_type',
                    'filter' => Order::getDeliveryTypesList(),
                    'value' => function ($model) {
                        return  Order::getDeliveryTypeName($model->delivery_type);
                    }
                ],
                [
                    'attribute' => 'payment_status',
                    'filter' => Order::getPaymentStatusesList(),
                    'value' => function ($model) {
                        return  Order::getPaymentStatusName($model->payment_status);
                    }
                ],
                [
                    'attribute' => 'created_with',
                    'value' => static function ($model) {
                        if ($model->created_with === 'site') {
                            return "<i class='fa fa-laptop'></i> " . "сайт";
                        } elseif ($model->created_with === 'mobile') {
                            return "<i class='fa fa-mobile'></i> " . "додаток";
                        } else {
                            return '';
                        }
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'rating',
                    'format' => 'raw',
                    'value' => static function ($model) {
                        return StarRating::widget([
                            'name' => 'rating',
                            'value' => $model->rating,
                            'pluginOptions' => [
                                'readonly' => true,
                                'showClear' => false,
                                'showCaption' => false,
                            ],
                        ]);
                    }
                ], [
                    'attribute' => 'status',
                    'filter' => Order::getStatusesList(),
                    'value' => function ($model) {
                        return  Order::getStatusName($model->status);
                    }
                ], [
                    'attribute' => 'total',
                    'value' => function ($model) {
                        return  Currency::format($model->total, 'UAH');
                    }
                ], [
                    'attribute' => 'restaurant_id',
                    'value' => static function ($order) {
                        /** @var Order $order */
                        return $order->getRestaurantName();
                    }
                ], [
                    'attribute' => 'time',
                    'format' => ['datetime', 'php:d.m.Y H:i'],
                    'filter' => false,
                ], [
                    'attribute' => 'created_at',
                    'format' => ['datetime', 'php:d.m.Y H:i'],
                    'filter' => false,
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => ['datetime', 'php:d.m.Y H:i'],
                    'filter' => false,
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{print} {update} {delete}',
                    'buttons' => [
                        'print' => static function ($url, $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-print"></span>',
                                $url,
                                [
                                    'title' => 'Распечатать',
                                    'aria-label' => 'Распечатать',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Вы уверены, что хотите распечатать этот заказ?',
                                    'data-method' => 'post',
                                ]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
        <?php Pjax::end() ?>
    </div>
</div>
<?php
$this->registerJs('
    // Update grid every 1 minute
    setInterval(function() {
         $.pjax.reload({container: "#order-pjax-container"});
    }, 60000);', View::POS_HEAD);
?>

