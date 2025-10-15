<?php
/**
 * Category index view.
 */

use app\module\admin\models\Restaurant;
use app\module\admin\module\product\models\Category;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\module\product\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index box box-primary">
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
                [
                    'attribute' => 'categoryName',
                    'format' => 'raw',
                    'value' => static function ($model) {
                        /** @var Category $model */
                        if ($model->name) {
                            $label = $model->categoryName;
                            $url =  "<a href="
                                .Url::to([
                                    '/admin/product/product',
                                    'ProductSearch[categoryName]' => $model->category_id
                                ], true) . ">$label</a> ";
                            return $url;
                        }
                        return '';
                    }
                ],
                [
                    'attribute' => 'restaurant_id',
                    'filter' => Restaurant::getList(),
                    'value' => static function ($model) {
                        /** @var Category $model */
                        return  $model->restaurant ? $model->restaurant->restaurantTitle : '';
                    }
                ],
                [
                    'attribute' => 'status',
                    'filter' => Category::getStatusesList(),
                    'value' => function ($model) {
                        return  Category::getStatusName($model->status);
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
