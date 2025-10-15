<?php
/**
 * Banner create view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Vacancy */
/* @var $languages array */
/* @var $descriptions array */
/* @var $dataProviders array */
/* @var $errors array */

$this->title = 'Добавить вакансию';
$this->params['breadcrumbs'][] = ['label' => 'Вакансии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banner-create">
    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'dataProviders' => $dataProviders,
        'descriptions' => $descriptions,
        'errors' => $errors,
    ]) ?>
</div>
