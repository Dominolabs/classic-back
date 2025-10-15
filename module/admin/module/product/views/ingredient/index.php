<?php
/**
 * @author Vitaliy Viznyuk <vitaliyviznyuk@gmail.com>
 * @copyright Copyright (c) 2019 Vitaliy Viznyuk
 */

use app\assets\ProductIndexAsset;
use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\product\models\Ingredient;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\module\product\models\IngredientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ингредиенты';
$this->params['breadcrumbs'][] = $this->title;
ProductIndexAsset::register($this);
?>
<div class="ingredient-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success btn-flat']) ?>
        <?= Html::a('Загрузить ПБ', '/admin/product/product/upload-pb', [
            'class' => 'action-upload-pb btn btn-success btn-flat',
            'data-type' => 'ingredient'
        ]) ?>
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
                        /** @var Ingredient $data */
                        $url = $data->resizeImage($data->image, 40, 40);
                        return Html::img($url, ['class' => 'img-thumbnail']);
                    },
                    'contentOptions' => ['class' => 'text-center'],
                ],
                'name',
                'categoryName',
                'portionSize',
                [
                    'attribute' => 'price',
                    'value' => function ($model) {
                        return  Currency::format($model->price, 'UAH');
                    }
                ],
                [
                    'attribute' => 'status',
                    'filter' => Ingredient::getStatusesList(),
                    'value' => function ($model) {
                        return Ingredient::getStatusName($model->status);
                    }
                ],
                'sort_order',
                [
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
                    'template' => '{update} {delete}',
                ],
            ],
        ]); ?>
    </div>
</div>

<div class="modal fade" id="upload-pb" tabindex="-1" role="dialog" aria-labelledby="upload-pbLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="upload-pbLabel">Загрузить данные с файла</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    Файл должен быть в <strong>csv</strong> формате. Первая строка должа содержать заголоки.
                    Должнен быть столбец с названием "<strong>id</strong>" и с названием "<strong>name</strong>"
                </div>
                <div class="js-alerts alert" style="display: none"></div>
                <div style="display: flex; justify-content: flex-start">
                    <div style="margin-right: 10px">
                        <?= Html::fileInput('upload-pb-file', '', [
                            'type' => 'file',
                            'accept' => 'text/csv',
                            'class' => 'js-pb-file-input',
                            'style' => 'display:none'
                        ]) ?>
                        <button type="button" class="btn btn-primary action-check-pb-file">Выбрать файл</button>
                    </div>
                    <div class="col col-90">
                        <div class="js-pb-file-name"></div>
                        <div class="js-no-file">Файл не выбран</div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" disabled class="btn btn-primary action-do-upload-pb">Загрузить</button>
            </div>
        </div>
    </div>
</div>