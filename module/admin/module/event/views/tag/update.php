<?php
/**
 * Banner update view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Banner */
/* @var $languages array */
/* @var $descriptions array */
/* @var $errors array */

$this->title = 'Изменить таг';
$this->params['breadcrumbs'][] = ['label' => 'Вакансии', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить таг';
?>
<div class="banner-update">
    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'descriptions' => $descriptions,
        'errors' => $errors
    ]) ?>
</div>
