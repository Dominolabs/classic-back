<?php
/**
 * SeoUrl index view.
 */

use app\module\admin\models\Language;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\models\SeoUrlSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'SEO URL';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seo-url-index box box-primary">
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

                'query',
                'keyword',
                [
                    'attribute' => 'language_id',
                    'filter' => Language::getList(),
                    'value' => function ($model) {
                        return  Language::getLanguageName($model['language_id']);
                    }
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                ],
            ],
        ]); ?>
    </div>
</div>
