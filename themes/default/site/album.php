<?php

use app\module\admin\module\gallery\models\AlbumImage;
use yii\helpers\Url;

/* @var $backUrl string */
/* @var $album array */
/* @var $albumImages array */
/* @var $this yii\web\View */

$this->title = $album['name'];
?>
<div class="page-content__content  album-page">
    <h1 class="album-page__title">
        <a href="<?= $backUrl ?>" class="album-page__title--back">
            <svg width="31px" height="22px">
                <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-arrow-2"></use>
            </svg>
        </a>
        <?= $album['name'] ?>
    </h1>
    <ul class="album-page__list">
        <?php foreach ($albumImages as $albumImage): ?>
        <li class="album-page__item">
            <img src="<?= AlbumImage::getImageUrl($albumImage['image'], 600, 600) ?>" alt=""
                 class="album-page__item-img"
                 data-large-img="<?= Yii::$app->request->baseUrl . '/image/' . $albumImage->imageDirectory . '/' . $albumImage['image'] ?>">
            <svg class="album-page__item-zoom" width="22px" height="22px">
                <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-zoom"></use>
            </svg>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
