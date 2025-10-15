<?php

use app\module\admin\module\event\models\Event;
use app\module\admin\module\event\models\EventCategory;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\module\event\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index box box-primary">
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

                [
                    'attribute' => 'imageFile',
                    'format' => 'image',
                    'content' =>  function($data) {
                        $url = $data->resizeImage($data->image, 40, 40);
                        return Html::img($url, ['class' => 'img-thumbnail']);
                    },
                    'contentOptions' => ['class' => 'text-center'],
                ],
                'eventName',
                'eventDate',
                [
                    'attribute' => 'event_category_id',
                    'filter' => EventCategory::getList(),
                    'value' => function ($model) {
                        return  $model->eventCategoryDescription->name;
                    }
                ], [
                    'attribute' => 'status',
                    'filter' => Event::getStatusesList(),
                    'value' => function ($model) {
                        return  Event::getStatusName($model->status);
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
