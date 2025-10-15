<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\pizzeria\models\Pizzeria */
/* @var $languages array */
/* @var $descriptions array */
/* @var $placeholder string */

$this->title = 'Добавить пиццерию';
$this->params['breadcrumbs'][] = ['label' => 'Пиццерии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pizzeria-create">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'descriptions' => $descriptions,
        'placeholder' => $placeholder
    ]) ?>

</div>
