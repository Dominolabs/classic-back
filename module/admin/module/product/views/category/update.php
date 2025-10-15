<?php
/**
 * Category update view.
 */

/* @var $this yii\web\View */
/* @var $category app\module\admin\module\product\models\Category */
/* @var $descriptions array */
/* @var $languages array */
/* @var $error string */
/* @var $seoUrls array */
/* @var $searchModel app\module\admin\module\product\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $topProduct Product*/
/* @var $operation_id int*/

$this->title = 'Изменить категорию';
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить категорию';

use app\module\admin\module\product\models\Product; ?>
<div class="category-update">
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
