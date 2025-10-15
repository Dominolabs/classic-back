<?php
/**
 * Banner update view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\Banner */
/* @var $placeholder string */
/* @var $errors array */

$this->title = 'Изменить информацию о подписчике';
$this->params['breadcrumbs'][] = ['label' => 'Подписчики', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить информацию о подписчике';
?>
<div class="banner-update">
    <?= $this->render('_form', [
        'model'       => $model,
        'errors'      => $errors,
    ]) ?>
</div>
