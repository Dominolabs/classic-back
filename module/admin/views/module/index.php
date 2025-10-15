<?php
/**
 * Module index view.
 */

use app\module\admin\models\Module;
use kartik\file\FileInput;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\models\ModuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Модули';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-index box box-primary">
    <div class="box-header with-border">
        <?php $form = ActiveForm::begin([
            'id' => 'upload-form',
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>

            <?= $form->field($model, 'file')->widget(FileInput::class, [
                'options' => [
                    'accept' => 'application/zip',
                    'multiple' => false,
                ],
                'pluginOptions' => [
                    'showPreview' => false,
                    'msgPlaceholder' => 'Выберите zip-архив модуля...',
                    'uploadUrl' => Url::to(['index']),
                ],
            ]); ?>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="box-body table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'title',
                'author',
                'version',
                [
                    'attribute' => 'status',
                    'filter' => Module::getStatusesList(),
                    'value' => function ($model) {
                        return  Module::getStatusName($model->status);
                    }
                ], [
                    'class' => 'kartik\grid\EditableColumn',
                    'attribute' => 'sort_order',
                    'refreshGrid' => true,
                    'editableOptions' => function ($model, $key, $index) {
                        return [
                            'header' => 'порядок сортировки',
                            'formOptions' => [
                                'action' => ['editSortOrder'],
                            ],
                            'options' => [
                                'autocomplete' => 'off',
                            ],
                        ];
                    },
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{activate} {deactivate} {delete}',
                    'buttons' => [
                        'activate' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-plus-sign"></span>',
                                $url,
                                [
                                    'title' => 'Активировать',
                                    'aria-label' => 'Активировать',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Вы уверены, что хотите активировать этот модуль?',
                                    'data-method' => 'post',
                                ]
                            );
                        },
                        'deactivate' => function ($url, $model, $key) {
                            return Html::a(
                                '<span class="glyphicon glyphicon-minus-sign"></span>',
                                $url,
                                [
                                    'title' => 'Деактивировать',
                                    'aria-label' => 'Деактивировать',
                                    'data-pjax' => 0,
                                    'data-confirm' => 'Вы уверены, что хотите деактивировать этот модуль?',
                                    'data-method' => 'post',
                                ]
                            );
                        }
                    ],
                    'visibleButtons' => [
                        'activate' => function ($model, $key, $index) {
                            return ($model->status == Module::STATUS_NOT_ACTIVE) ? true : false;
                        },
                        'deactivate' => function ($model, $key, $index) {
                            return ($model->status == Module::STATUS_ACTIVE) ? true : false;
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
