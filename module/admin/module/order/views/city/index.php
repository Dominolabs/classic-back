<?php

use app\module\admin\module\currency\models\Currency;
use yii\helpers\Html;
use yii\grid\GridView;
use app\module\admin\module\order\models\City;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\module\order\models\CitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Населенные пункты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <div class="box-body table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'cityName',
                [
                    'attribute' => 'delivery_price',
                    'value' => static function ($model) {
                        return  Currency::format($model->delivery_price, 'UAH');
                    }
                ],
                [
                    'attribute' => 'minimum_order',
                    'value' => static function ($model) {
                        return  Currency::format($model->minimum_order, 'UAH');
                    }
                ],
                [
                    'attribute' => 'free_minimum_order',
                    'value' => static function ($model) {
                        return  Currency::format($model->free_minimum_order, 'UAH');
                    }
                ],
                [
                    'attribute' => 'status',
                    'filter' => City::getStatusesList(),
                    'value' => static function ($model) {
                        return  City::getStatusName($model->status);
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
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                ],
            ],
        ]); ?>
    </div>
</div>
