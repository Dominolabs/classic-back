<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\module\gallery\models\Album */
/* @var $languages array */
/* @var $descriptions array */
/* @var $albumImagesDataProvider yii\data\ArrayDataProvider */
/* @var $placeholder string */
/* @var $errors array */
/* @var $seoUrls array */
/* @var $initialPreview array */
/* @var $initialPreviewConfig array */

$this->title = 'Добавить галерею';
$this->params['breadcrumbs'][] = ['label' => 'Галереи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="album-create">
    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'descriptions' => $descriptions,
        'albumImagesDataProvider' => $albumImagesDataProvider,
        'placeholder' => $placeholder,
        'errors' => $errors,
        'seoUrls' => $seoUrls,
        'initialPreview' => $initialPreview,
        'initialPreviewConfig' => $initialPreviewConfig,
    ]) ?>
</div>
