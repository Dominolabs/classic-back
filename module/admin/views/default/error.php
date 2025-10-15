<?php
/**
 * Error page view.
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<section class="content">
    <div class="error-page">
        <h2 class="headline text-info"><i class="fa fa-warning text-yellow"></i></h2>
        <div class="error-content">
            <h3><?= $name ?></h3>
            <p>
                <?= nl2br(Html::encode($message)) ?>
            </p>
            <p>
                Вышеупомянутая ошибка возникла, когда веб-сервер обрабатывал ваш запрос.
                Если вы считаете, что это ошибка сервера, свяжитесь с нами. Спасибо.
                Вы можете <a href='<?= Yii::$app->homeUrl ?>'>вернуться в панель управления</a>.
            </p>
        </div>
    </div>
</section>
