<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\currency\models\Currency */

$this->title = 'Изменить валюту';
$this->params['breadcrumbs'][] = ['label' => 'Валюта', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить валюту';
?>
<div class="currency-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
