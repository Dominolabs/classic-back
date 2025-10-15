<?php
/**
 * Banner update view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Banner */
/* @var $languages array */
/* @var $dataProviders array */
/* @var $placeholder string */
/* @var $errors array */

$this->title = 'Изменить баннер';
$this->params['breadcrumbs'][] = ['label' => 'Баннеры', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить баннер';
?>
<div class="banner-update">
    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'dataProviders' => $dataProviders,
        'placeholder' => $placeholder,
        'errors' => $errors,
    ]) ?>
</div>
