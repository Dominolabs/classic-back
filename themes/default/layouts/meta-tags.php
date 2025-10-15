<?php

use app\module\admin\models\DesignForm;
use yii\helpers\Url;

/* @var $title string */
/* @var $this \yii\web\View */

$favicon = !empty(Yii::$app->params['favicon']) ? DesignForm::getImageUrl(Yii::$app->params['favicon'], 32,
    32) : '/favicon.ico';

$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => $favicon]);

if (!empty($this->params['metaDescription'])) {
    $this->registerMetaTag([
        'name' => 'description',
        'content' => $this->params['metaDescription'],
    ]);
}

if (!empty($this->params['metaKeywords'])) {
    $this->registerMetaTag([
        'name' => 'keywords',
        'content' => $this->params['metaKeywords'],
    ]);
}

$this->registerMetaTag([
    'name' => 'og:title',
    'content' => $title,
]);

if (!empty($this->params['metaDescription'])) {
    $this->registerMetaTag([
        'name' => 'og:description',
        'content' => $this->params['metaDescription'],
    ]);
}

$this->registerMetaTag([
    'name' => 'og:image:secure_url',
    'content' => !empty($this->params['backgroundImage']) ? Url::to($this->params['backgroundImage'],
        'https') : Url::to('/img/jpg/main-bg.jpg', 'https'),
]);

$this->registerMetaTag([
    'name' => 'og:image',
    'content' => !empty($this->params['backgroundImage']) ? Url::to($this->params['backgroundImage'],
        'https') : Url::to('/img/jpg/main-bg.jpg', 'https'),
]);

$this->registerMetaTag([
    'name' => 'og:type',
    'content' => 'website',
]);

$this->registerMetaTag([
    'name' => 'og:url',
    'content' => Url::to('', 'https'),
]);

$this->registerMetaTag([
    'name' => 'og:locale',
    'content' => Yii::$app->language == 'uk' ? 'uk_UA' : (Yii::$app->language == 'en' ? 'en_US' : Yii::$app->language),
]);
