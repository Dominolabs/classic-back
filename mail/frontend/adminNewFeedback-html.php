<?php

/* @var $this yii\web\View */
/* @var $feedback Feedback */
?>
<div class="password-reset">
    <p>Вы получили новый отзыв.</p>
    <p>№ отзыва: <?= $feedback->feedback_id ?></p>
    <p>Дата отзыва: <?= Yii::$app->formatter->asDate($feedback->created_at, 'php:d.m.Y H:i') ?></p>
    <p>Имя клиента: <?= $feedback->name ?></p>
    <p>Email: <?= $feedback->email ?></p>
    <p>Номер телефона: <?= $feedback->phone ?></p>
    <p>Текст: <?= $feedback->text ?></p>
</div>
