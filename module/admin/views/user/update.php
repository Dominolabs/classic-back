<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\User */
/* @var $placeholder string */

$this->title = 'Изменить пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить пользователя';
?>
<div class="user-update">
    <?= $this->render('_form', [
        'model' => $model,
        'placeholder' => $placeholder
    ]) ?>
</div>
