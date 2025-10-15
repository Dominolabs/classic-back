<?php
/**
 * Language update view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Language */

$this->title = 'Изменить язык';
$this->params['breadcrumbs'][] = ['label' => 'Языки', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить язык';
?>
<div class="language-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
