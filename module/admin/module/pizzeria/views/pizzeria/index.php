<?php

use app\module\admin\module\pizzeria\models\Pizzeria;
use app\module\admin\module\pizzeria\models\PizzeriaCategory;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\module\pizzeria\models\PizzeriaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пиццерии';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pizzeria-index box box-primary">
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
                'pizzeriaName',
                'email:email',
                [
                    'attribute' => 'instagram',
                    'format' => 'raw',
                    'value'=>function ($data) {
                        return Html::a($data->instagram, $data->instagram, ['target' => '_blank']);
                    },

                ],
                [
                    'attribute' => 'status',
                    'filter' => Pizzeria::getStatusesList(),
                    'value' => function ($model) {
                        return  Pizzeria::getStatusName($model->status);
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
