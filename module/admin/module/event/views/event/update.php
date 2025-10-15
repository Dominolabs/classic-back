<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\event\models\Event */
/* @var $languages array */
/* @var $descriptions array */
/* @var $placeholder string */

$this->title = 'Изменить новость';
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить новость';
?>
<div class="event-update">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'descriptions' => $descriptions,
        'placeholder' => $placeholder
    ]) ?>

</div>
