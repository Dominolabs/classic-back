<?php
/**
 * SeoUrl update view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\SeoUrl */

$this->title = 'Изменить SEO URL';
$this->params['breadcrumbs'][] = ['label' => 'SEO URL', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить SEO URL';
?>
<div class="seo-url-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
