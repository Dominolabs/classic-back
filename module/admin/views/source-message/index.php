<?php
/**
 * SourceMessage index view.
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\models\SourceMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Переводы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-message-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success btn-flat']) ?>
        <?= Html::a('Очистить кеш', ['flush-cache'], ['class' => 'btn btn-warning btn-flat']) ?>
    </div>
    <div class="box-body table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'category',
                'message:ntext',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                ],
            ],
        ]); ?>
    </div>
</div>
