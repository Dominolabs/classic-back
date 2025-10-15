<?php

/* @var $this yii\web\View */
/* @var $product app\module\admin\models\Classic */
/* @var $descriptions array */
/* @var $languages array */
/* @var $placeholder string */
/* @var $seoUrls array */

$this->title = 'Изменить товар';
$this->params['breadcrumbs'][] = 'Изменить товар';
?>
<div class="product-update">
    <?= $this->render('_form', [
        'product' => $product,
        'descriptions' => $descriptions,
        'languages' => $languages,
        'placeholder' => $placeholder,
        'seoUrls' => $seoUrls,
    ]) ?>
</div>
