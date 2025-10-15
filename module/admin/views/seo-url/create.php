<?php
/**
 * SeoUrl create view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\SeoUrl */

$this->title = 'Добавить SEO URL';
$this->params['breadcrumbs'][] = ['label' => 'SEO URL', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seo-url-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
