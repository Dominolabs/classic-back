<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\User */
/* @var $placeholder string */

$this->title = 'Добавить пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <?= $this->render('_form', [
        'model' => $model,
        'placeholder' => $placeholder
    ]) ?>
</div>
