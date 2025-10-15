<?php
/**
 * Banner update view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Banner */
/* @var $placeholder string */
/* @var $errors array */

$this->title = 'Изменить заявку на вакансию';
$this->params['breadcrumbs'][] = ['label' => 'Вакансии (заявки)', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить заявку на вакансию';
?>
<div class="banner-update">
    <?= $this->render('_form', [
        'model'       => $model,
        'errors'      => $errors,
        'placeholder' => $placeholder
    ]) ?>
</div>
