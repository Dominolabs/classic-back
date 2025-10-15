<?php
/**
 * Language index view.
 */

use app\module\admin\models\Language;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\models\LanguageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Языки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-index box box-primary">
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

                'name',
                'code',
                [
                    'attribute' => 'imageFile',
                    'format' => 'image',
                    'value' => function($data) {
                        return $data->resizeImage($data->image, 16, 16);
                    },
                ], [
                    'attribute' => 'status',
                    'filter' => Language::getStatusesList(),
                    'value' => function ($model) {
                        return  Language::getStatusName($model->status);
                    }
                ],
                'sort_order',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'visibleButtons' => [
                        'delete' => function ($model, $key, $index) {
                            return (Language::getAllCount() > 1) ? true : false;
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
