<?php

use app\module\admin\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index box box-primary">
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
                    'attribute' => 'avatarFile',
                    'format' => 'avatar',
                    'content' =>  function($data) {
                        $url = $data->resizeImage($data->avatar, 40, 40);
                        return Html::img($url, ['class' => 'img-thumbnail']);
                    },
                    'contentOptions' => ['class' => 'text-center'],
                ],
                [
                    'attribute' => 'username',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $url = "<a href=\""
                            . Url::to('/admin/order/order?OrderSearch[user_id]='.$model->user_id)
                            . "\">$model->name</a> ";
                        return $url;
                    }
                ],
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $url = "<a href=\""
                            .Url::to('/admin/order/order?OrderSearch[user_id]='.$model->user_id)
                            . "\">$model->name</a> ";
                        return $url;
                    }
                ],
                'email:email',
                'phone',
                [
                    'attribute' => 'role',
                    'filter' => User::getRolesList(),
                    'value' => function ($model) {
                        return  User::getRoleName($model->role);
                    }
                ], [
                    'attribute' => 'status',
                    'filter' => User::getStatusesList(),
                    'value' => function ($model) {
                        return  User::getStatusName($model->status);
                    }
                ],
                [
                    'attribute' => 'ordersCount',
                    'label' => 'Количество заказов',
                    'format' => 'raw',
                    'filter' => false,
                    'value' => function ($model) {
                        $url =  "<a href=\""
                            . Url::to('/admin/order/order?OrderSearch[user_id]='.$model->user_id)
                            . "\">$model->ordersCount</a> ";
                        return $url;
                    }
                ],
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
                    'visibleButtons' => [
                        'delete' => function ($model, $key, $index) {
                            return (User::getAllCount() > 1) ? true : false;
                        }
                    ]
                ],
            ],
        ]); ?>
    </div>
</div>
