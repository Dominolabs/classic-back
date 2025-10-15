<?php
/**
 * Banner update view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Banner */
/* @var $languages array */
/* @var $descriptions array */
/* @var $errors array */

$this->title = 'Изменить вакансию';
$this->params['breadcrumbs'][] = ['label' => 'Вакансии', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить вакансию';
?>
<div class="banner-update">
    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'descriptions' => $descriptions,
        'errors' => $errors
    ]) ?>
</div>
