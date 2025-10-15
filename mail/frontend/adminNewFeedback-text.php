<?php

/* @var $this yii\web\View */
/* @var $feedback Feedback */
?>
Вы получили новый отзыв.

Вы получили новый отзыв.
№ отзыва: <?= $feedback->feedback_id ?>
Дата отзыва: <?= Yii::$app->formatter->asDate($feedback->created_at, 'php:d.m.Y H:i') ?>
Имя клиента: <?= $feedback->name ?>
Email: <?= $feedback->email ?>
Номер телефона: <?= $feedback->phone ?>
Текст: <?= $feedback->text ?>
