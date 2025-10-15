<?php

use app\module\admin\module\feedback\models\Feedback;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\module\admin\module\feedback\models\FeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отзывы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-index box box-primary">
   <div class="box-body table-responsive">
       <?= GridView::widget([
           'dataProvider' => $dataProvider,
           'filterModel' => $searchModel,
           'layout' => "{items}\n{summary}\n{pager}",
           'columns' => [
               ['class' => 'yii\grid\SerialColumn'],
               [
                   'attribute' => 'name',
                   'label' => 'Имя'
               ],
               'phone',
               'email',
               [
                   'attribute' => 'text',
                   'label' => 'Текст сообщения'
               ],
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
