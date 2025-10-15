<?php

/* @var $this yii\web\View */
/* @var $product app\module\admin\module\product\models\Product */
/* @var $productToCategory app\module\admin\module\product\models\ProductToCategory */
/* @var $descriptions array */
/* @var $languages array */
/* @var $placeholder string */
/* @var $is_create bool */
/* @var $seoUrls array */

$this->title = 'Добавить товар';
$this->params['breadcrumbs'][] = ['label' => 'Товары', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-create">
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
