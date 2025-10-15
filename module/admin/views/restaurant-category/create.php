<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\RestaurantCategory */
/* @var $descriptions array */
/* @var $languages array */
/* @var $seoUrls array */

$this->title = 'Добавить категорию';
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="restaurant-category-create">
    <?= $this->render('_form', [
        'model' => $model,
        'descriptions' => $descriptions,
        'languages' => $languages,
        'seoUrls' => $seoUrls,
    ]) ?>
</div>
