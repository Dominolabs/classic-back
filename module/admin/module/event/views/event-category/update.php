<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\event\models\EventCategory */
/* @var $descriptions array */
/* @var $languages array */

$this->title = 'Изменить категорию';
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить категорию';
?>
<div class="event-category-update">
    <?= $this->render('_form', [
        'model' => $model,
        'descriptions' => $descriptions,
        'languages' => $languages,
    ]) ?>
</div>
