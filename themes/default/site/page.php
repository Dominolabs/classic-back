<?php

/* @var $this yii\web\View */
/* @var $page array */
/* @var $defaultPage array */
/* @var $cateringBannerImages array */
/* @var $imageWidth int */
/* @var $imageHeight int */

$this->title = $page['meta_title'] ? $page['meta_title'] :
    ($defaultPage['meta_title'] ? $defaultPage['meta_title'] : ($page['title'] ? $page['title'] : $defaultPage['title']));

$metaDescription = $page['meta_description'] ? $page['meta_description'] : $defaultPage['meta_description'];
$metaKeywords = $page['meta_keyword'] ? $page['meta_keyword'] : $defaultPage['meta_keyword'];

$this->params['metaDescription'] = $metaDescription;
$this->params['metaKeywords'] = $metaKeywords;
?>
<div class="page-content__content  static-page">
    <h1 class="static-page__title">
        <?= $this->title ?>
    </h1>
    <div class="static-page__content">
        <?= !empty($page['content']) ? $page['content'] : $defaultPage['content'] ?>
    </div>
</div>
