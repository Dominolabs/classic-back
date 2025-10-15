<?php

/* @var $this yii\web\View */
/* @var $restaurant app\module\admin\models\Restaurant */
/* @var $descriptions array */
/* @var $seoUrls array */
/* @var $languages array */
/* @var $placeholder string */

$this->title = 'Изменить ресторан';
$this->params['breadcrumbs'][] = ['label' => 'Рестораны', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить ресторан';
?>
<div class="restaurant-update">
    <?= $this->render('_form', [
        'restaurant' => $restaurant,
        'descriptions' => $descriptions,
        'seoUrls' => $seoUrls,
        'languages' => $languages,
        'placeholder' => $placeholder
    ]) ?>
</div>
