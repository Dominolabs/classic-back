<?php

use app\module\admin\models\VacancyRequest;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\models\VacancyRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $placeholder */

$this->title = 'Вакансии (заявки)';
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
                   'attribute' => 'vacancyName',
                   'label'     => 'Вакансия'
               ],
               [
                   'attribute' => 'full_name',
                   'label'     => 'ФИО кандидата'
               ],
               [
                   'attribute'      => 'photoFile',
                   'label'          => 'Фото',
                   'format'         => 'image',
                   'content'        => function ($data) use($placeholder) {
                       $url = $data->resizeImage($data->photo, 80, 80);
                       if(!isset($url)){
                          $url = $placeholder;
                       }
                       return Html::img($url, ['class' => 'img-thumbnail', 'style' => 'width: 90px;']);
                   },
                   'contentOptions' => ['class' => 'text-center'],
               ],
               [
                   'attribute' => 'age',
                   'label'     => 'Возраст'
               ],
               [
                   'attribute' => 'phone',
                   'label'     => 'Телефон'
               ],
               [
                   'attribute' => 'email',
                   'label'     => 'Email'
               ],
               [
                   'attribute' => 'created_at',
                   'label'     => 'Получено',
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
