<?php

use app\module\admin\models\VacancyRequest;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\models\VacancyRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $placeholder */

$this->title = 'Подписчики';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banner-index box box-primary">
   <div class="box-body table-responsive">
       <?= GridView::widget([
           'dataProvider' => $dataProvider,
           'filterModel' => $searchModel,
           'layout' => "{items}\n{summary}\n{pager}",
           'columns' => [
               ['class' => 'yii\grid\SerialColumn'],
               [
                   'attribute' => 'name',
                   'label'     => 'Имя подписчика'
               ],
               [
                   'attribute' => 'email',
                   'label'     => 'Email'
               ],
               [
                   'attribute' => 'created_at',
                   'label'     => 'Подписан',
                   'format'    => ['datetime', 'php:d.m.Y H:i'],
                   'filter'    => false,
               ],
               [
                   'class'     => 'yii\grid\ActionColumn',
                   'template'  => '{update} {delete}',
               ],
           ],
       ]) ?>
   </div>
</div>
