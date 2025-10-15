<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\event\models\EventCategory */
/* @var $descriptions array */
/* @var $languages array */

$this->title = 'Добавить категорию';
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-category-create">
    <?= $this->render('_form', [
        'model' => $model,
        'descriptions' => $descriptions,
        'languages' => $languages,
    ]) ?>
</div>
