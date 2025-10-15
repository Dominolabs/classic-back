<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\currency\models\Currency */

$this->title = 'Добавить валюту';
$this->params['breadcrumbs'][] = ['label' => 'Валюта', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
