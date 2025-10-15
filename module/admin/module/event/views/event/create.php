<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\event\models\Event */
/* @var $languages array */
/* @var $descriptions array */
/* @var $placeholder string */
/* @var $errors string */

$this->title = 'Добавить новость';
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-create">

    <?= $this->render('_form', [
        'model'        => $model,
        'languages'    => $languages,
        'descriptions' => $descriptions,
        'placeholder'  => $placeholder,
        'errors'       => $errors
    ]) ?>

</div>
