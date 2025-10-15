<?php
/**
 * Category create view.
 */

/* @var $this yii\web\View */
/* @var $category app\module\admin\module\product\models\Category */
/* @var $descriptions array */
/* @var $languages array */
/* @var $seoUrls array */
/* @var $error string */
/* @var $searchModel app\module\admin\module\product\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $topProduct Product*/
/* @var $operation_id int*/

$this->title = 'Добавить категорию';
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

use app\module\admin\module\product\models\Product; ?>
<div class="category-create">
    <?= $this->render('_form', [
        'category' => $category,
        'descriptions' => $descriptions,
        'languages' => $languages,
        'seoUrls' => $seoUrls,
        'error' => $error,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'topProduct' => $topProduct,
        'operation_id' => $operation_id,
    ]) ?>
</div>
