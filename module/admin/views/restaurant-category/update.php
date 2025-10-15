<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\RestaurantCategory */
/* @var $descriptions array */
/* @var $languages array */
/* @var $seoUrls array */

$this->title = 'Изменить категорию';
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить категорию';
?>
<div class="restaurant-category-update">
    <?= $this->render('_form', [
        'model' => $model,
        'descriptions' => $descriptions,
        'languages' => $languages,
        'seoUrls' => $seoUrls,
    ]) ?>
</div>
