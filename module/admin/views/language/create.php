<?php
/**
 * Language create view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Language */

$this->title = 'Добавить язык';
$this->params['breadcrumbs'][] = ['label' => 'Языки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-create">
    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>
</div>
