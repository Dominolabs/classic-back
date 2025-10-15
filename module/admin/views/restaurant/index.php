<?php use yii\grid\ActionColumn;

use yii\grid\SerialColumn;
use app\module\admin\models\Restaurant;
use app\module\admin\models\RestaurantCategory;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\models\RestaurantSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Рестораны';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="restaurant-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <div class="box-body table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                ['class' => SerialColumn::class],

                'restaurantName',
                [
                    'attribute' => 'restaurant_category_id',
                    'filter' => RestaurantCategory::getList(),
                    'value' => static function ($model) {
                        /** @var Restaurant $model */
                        return  $model->restaurantCategory ? $model->restaurantCategory->restaurantCategoryName : '';
                    }
                ],
                [
                    'attribute' => 'online_delivery',
                    'filter' => Restaurant::getOnlineDeliveryList(),
                    'value' => static function ($model) {
                        /** @var Restaurant $model */
                        return  Restaurant::getOnlineDeliveryName($model->online_delivery);
                    }
                ],
                [
                    'attribute' => 'online_delivery_orders_processing',
                    'filter' => Restaurant::getOnlineDeliveryList(),
                    'value' => static function ($model) {
                        /** @var Restaurant $model */
                        return  Restaurant::getOnlineDeliveryName($model->online_delivery_orders_processing);
                    }
                ],
                [
                    'attribute' => 'self_picking',
                    'filter' => Restaurant::getOnlineDeliveryList(),
                    'value' => static function ($model) {
                        /** @var Restaurant $model */
                        return  Restaurant::getOnlineDeliveryName($model->self_picking);
                    }
                ],
                [
                    'attribute' => 'status',
                    'filter' => Restaurant::getStatusesList(),
                    'value' => static function ($model) {
                        return  Restaurant::getStatusName($model->status);
                    }
                ],
                'sort_order',
                [
                    'attribute' => 'created_at',
                    'format' => ['datetime', 'php:d.m.Y H:i'],
                    'filter' => false,
                ], [
                    'attribute' => 'updated_at',
                    'format' => ['datetime', 'php:d.m.Y H:i'],
                    'filter' => false,
                ],

                [
                    'class' => ActionColumn::class,
                    'template' => '{update} {delete}',
                ],
            ],
        ]); ?>
    </div>
</div>
