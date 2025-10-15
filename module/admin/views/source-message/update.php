<?php
/**
 * SourceMessage update view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\SourceMessage */
/* @var $languages array */
/* @var $messages array */

$this->title = 'Изменить перевод';
$this->params['breadcrumbs'][] = ['label' => 'Переводы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить Перевод';
?>
<div class="source-message-update">
    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'messages' => $messages,
    ]) ?>
</div>
