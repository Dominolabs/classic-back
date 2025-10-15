<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\order\models\City */
/* @var $descriptions array */
/* @var $languages array */

$this->title = 'Добавить город';
$this->params['breadcrumbs'][] = ['label' => 'Населенные пункты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="city-create">

    <?= $this->render('_form', [
        'model' => $model,
        'descriptions' => $descriptions,
        'languages' => $languages,
    ]) ?>

</div>
