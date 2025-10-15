<?php

use app\module\admin\models\Page;
use yii\helpers\Url;

/* @var $page array */
/* @var $defaultPage array */
/* @var $imageWidth int */
/* @var $imageHeight int */
/* @var $this yii\web\View */

$this->title = $page['meta_title'] ? $page['meta_title'] :
    ($defaultPage['meta_title'] ? $defaultPage['meta_title'] : ($page['title'] ? $page['title'] : $defaultPage['title']));

$metaDescription = $page['meta_description'] ? $page['meta_description'] : $defaultPage['meta_description'];
$metaKeywords = $page['meta_keyword'] ? $page['meta_keyword'] : $defaultPage['meta_keyword'];

$this->params['metaDescription'] = $metaDescription;
$this->params['metaKeywords'] = $metaKeywords;
$this->params['backgroundImage'] = !empty($page['image']) ? Page::getImageUrl($page['image'], $imageWidth, $imageHeight) : '../img/jpg/main-bg.jpg';

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-content__first-block  coming-soon-page" style="background-image: url('<?= $this->params['backgroundImage'] ?>')">
    <h1 class="coming-soon-page__title">
        <svg class="coming-soon-page__title--ico-desc">
            <use xlink:href="<?= Url::to('/img/svg/sprite-coming-soon.svg') ?>#ico-one-row"></use>
        </svg>
        <svg class="coming-soon-page__title--ico-mob">
            <use xlink:href="<?= Url::to('/img/svg/sprite-coming-soon.svg') ?>#ico-two-rows"></use>
        </svg>
    </h1>
</div>
