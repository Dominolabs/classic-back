<?php
/**
 * @author Vitaliy Viznyuk <vitaliyviznyuk@gmail.com>
 * @copyright Copyright (c) 2019 Vitaliy Viznyuk
 */

/* @var $this yii\web\View */
/* @var $ingredient app\module\admin\module\product\models\Ingredient */
/* @var $descriptions array */
/* @var $languages array */
/* @var $placeholder string */

$this->title = 'Добавить ингредиент';
$this->params['breadcrumbs'][] = ['label' => 'Ингредиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ingredient-create">
    <?= $this->render('_form', [
        'ingredient' => $ingredient,
        'descriptions' => $descriptions,
        'languages' => $languages,
        'placeholder' => $placeholder,
    ]) ?>
</div>
