<?php
/**
 * SourceMessage create view.
 */

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\SourceMessage */
/* @var $languages array */
/* @var $messages array */

$this->title = 'Добавить перевод';
$this->params['breadcrumbs'][] = ['label' => 'Переводы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="source-message-create">
    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
        'messages' => $messages,
    ]) ?>
</div>
