<?php

use app\module\admin\models\SeoUrl;
use app\module\admin\module\product\models\Product;
use yii\helpers\Url;

/* @var $categories array */
/* @var $this yii\web\View */

$this->title = Yii::t('menu', 'Меню');
?>
<div class="page-content__content  menu-page">
    <?php if (!empty($categories)): ?>
    <div class="menu-page__menu-list  menu-list">
        <div class="menu-list__content">
            <h3 class="menu-list__title"><?= Yii::t('menu', 'Меню') ?></h3>
            <ul class="menu-list__list">
                <?php foreach ($categories as $category): ?>
                <li class="menu-list__item">
                    <a href="#<?= SeoUrl::transliterate($category['name']) ?>" class="menu-list__link"><?= $category['name'] ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="menu-page__main-content">
        <?php foreach ($categories as $category): ?>
        <section class="menu-page__section" id="<?= SeoUrl::transliterate($category['name']) ?>">
            <h2 class="menu-page__section-title"><?= $category['name'] ?></h2>
            <?php if (!empty($category['products'])): ?>
            <div class="menu-page__prod-list">
                <?php foreach ($category['products'] as $product): ?>
                <article class="menu-page__prod  prev-prod">
                    <div class="prev-prod__img-wrap">
                        <img src="<?= Product::getImageUrl($product['image'], 600, 600) ?>" alt=""
                             class="prev-prod__img"
                             data-modal-full-img-link="<?= Product::getImageUrl($product['image']) ?>">
                        <svg class="prev-prod__zoom" width="22px" height="22px"
                             data-modal-full-img-link="<?= Product::getImageUrl($product['image']) ?>">
                            <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-zoom"></use>
                        </svg>
                    </div>
                    <h3 class="prev-prod__title"><?= $product['name'] ?></h3>
                    <div class="prev-prod__descr"><?= $product['description'] ?></div>
                    <div class="prev-prod__params-row">
                        <?php if ($product['caloricity'] > 0): ?>
                        <p class="prev-prod__energy"><?= $product['caloricity'] ?> <?= Yii::t('order', 'Ккал') ?></p>
                        <?php endif; ?>
                        <?php if ($product['weight'] > 0): ?>
                        <p class="prev-prod__weight"><?= $product['weight'] ?> <?= Yii::t('order', 'г') ?></p>
                        <?php endif; ?>
                        <p class="prev-prod__price"><?= Product::formatPrice($product['price'], 'UAH', false, '{value}') ?> <?= Yii::t('menu', 'грн') ?></p>
                    </div>
                    <a class="prev-prod__btn btn" data-modal-link="booking"><?= Yii::t('order', 'Замовити') ?></a>
                    <?php if (!empty($product['promo'])): ?>
                    <div class="prev-prod__share" data-dropdown>
                        <div class="prev-prod__share-content">
                            <p class="prev-prod__share-text"><?= $product['promo'] ?></p>
                        </div>
                        <span class="prev-prod__share-label" data-dropdown-link><?= Yii::t('order', 'Акція!') ?></span>
                    </div>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<div class="modal-full-img">
    <a class="modal-full-img__close">
        <svg width="26px" height="26px">
            <use xlink:href="<?= Url::to('/img/svg/sprite-main.svg') ?>#ico-close"></use>
        </svg>
    </a>
    <img src="" class="modal-full-img__img">
</div>
