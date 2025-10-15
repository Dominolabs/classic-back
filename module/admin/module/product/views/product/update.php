<?php

/* @var $this yii\web\View */
/* @var $product app\module\admin\module\product\models\Product */
/* @var $productToCategory app\module\admin\module\product\models\ProductToCategory */
/* @var $descriptions array */
/* @var $languages array */
/* @var $placeholder string */
/* @var $seoUrls array */
/* @var $is_create bool */

$this->title = 'Изменить товар';
$this->params['breadcrumbs'][] = ['label' => 'Товары', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить товар';
?>
<div class="product-update">
    <?= $this->render('_form', [
        'product' => $product,
        'productToCategory' => $productToCategory,
        'descriptions' => $descriptions,
        'languages' => $languages,
        'placeholder' => $placeholder,
        'seoUrls' => $seoUrls,
        'is_create' => $is_create
    ]) ?>
</div>
