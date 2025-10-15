<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = Yii::t('404', 'Нажаль сторінки не знайдено');
?>
<div class="page-content__content  error-page">
    <h1 class="error-page__title">
        <span class="error-page__label">
            <span class="error-page__label-text"><?= Yii::t('404', 'Ой!') ?></span>
        </span>
        404
    </h1>
    <h2 class="error-page__sub-title"><?= $this->title ?></h2>
    <p class="error-page__descr"><?= Yii::t('404', 'Сталась помилка. Ви можете спробувати оновити сторінку. Інколи це працює, або перейти на іншу сторінку сайту.') ?></p>
    <a href="<?= Url::to(['/']) ?>" class="error-page__btn btn"><?= Yii::t('404', 'Перейти на головну') ?></a>
</div>
