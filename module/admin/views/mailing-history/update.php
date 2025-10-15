<?php

/* @var $this yii\web\View */
/* @var $model app\module\admin\models\MailingHistory */

$this->title = 'Изменить Email';
$this->params['breadcrumbs'][] = ['label' => 'Email-расслыка', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
