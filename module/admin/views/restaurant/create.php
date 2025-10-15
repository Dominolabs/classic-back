<?php

/* @var $this yii\web\View */
/* @var $restaurant app\module\admin\models\Restaurant */
/* @var $descriptions array */
/* @var $seoUrls array */
/* @var $languages array */
/* @var $placeholder string */

$this->title = 'Добавить ресторан';
$this->params['breadcrumbs'][] = ['label' => 'Рестораны', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="restaurant-create">
    <?= $this->render('_form', [
        'restaurant' => $restaurant,
        'descriptions' => $descriptions,
        'seoUrls' => $seoUrls,
        'languages' => $languages,
        'placeholder' => $placeholder
    ]) ?>
</div>
