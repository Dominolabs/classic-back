<?php

use app\module\admin\module\gallery\models\Album;

/* @var $albums array */
/* @var $pageUrl string */
/* @var $pageTitle string */
/* @var $this yii\web\View */

$this->title = $pageTitle;
?>
<div class="page-content__content  gallery-page">
    <h1 class="gallery-page__title"><?= $pageTitle ?></h1>
    <ul class="gallery-page__list">
        <?php foreach ($albums as $album): ?>
        <li class="gallery-page__item">
            <a href="<?= Album::getUrl($album['album_id'], $pageUrl) ?>" class="gallery-page__item-link">
                <img src="<?= Album::getImageUrl($album['image'], 1374, 1007) ?>" alt="" class="gallery-page__item-img">
                <h2 class="gallery-page__item-title"><?= $album['name'] ?></h2>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
