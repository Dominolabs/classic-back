<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\models\NotificationsHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'PUSH-уведомления';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <div class="box-body table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                [
                        'class' => 'yii\grid\SerialColumn'
                ],
                'header',
                'message',
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
