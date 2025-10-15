<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\feedback\models\Feedback */

$this->title = 'Изменить PUSH-уведомление';
$this->params['breadcrumbs'][] = ['label' => 'PUSH-уведомления', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
