<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\feedback\models\Feedback */

$this->title = 'Изменить отзыв (обратная связь)';
$this->params['breadcrumbs'][] = ['label' => 'Отзывы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить отзыв (обратная связь)';
?>
<div class="team-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
