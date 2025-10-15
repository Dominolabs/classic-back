<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\module\admin\models\Language;
use app\module\admin\models\Page;
use app\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);

$languageId = Language::getLanguageIdByCode(Yii::$app->language);
$defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());

$titlePrefix = !empty(Yii::$app->params['titlePrefix'][$languageId]) ? Yii::$app->params['titlePrefix'][$languageId]
    : (!empty(Yii::$app->params['titlePrefix'][$defaultLanguageId]) ? Yii::$app->params['titlePrefix'][$defaultLanguageId] : '');
$titlePostfix = !empty(Yii::$app->params['titlePostfix'][$languageId]) ? Yii::$app->params['titlePostfix'][$languageId]
    : (!empty(Yii::$app->params['titlePostfix'][$defaultLanguageId]) ? Yii::$app->params['titlePostfix'][$defaultLanguageId] : '');

if (!Page::isHomePage()) {
    $this->title = $titlePrefix . $this->title . $titlePostfix;
}
$session = Yii::$app->session;
?>
<?= $this->render('meta-tags', ['title' => $this->title]) ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, shrink-to-fit=no" />
    <meta name="apple-itunes-app" content="app-id=1425018514">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-128070591-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag () {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-128070591-1');
    </script>
</head>
<body>
    <?php $this->beginBody() ?>
    <main class="page-content">
        <div class="modal__bg" id="overlay"></div>
        <?= $this->render('modals/side-nav') ?>
        <?= $this->render('modals/booking') ?>
        <?= $this->render('modals/delivery') ?>
        <?= $this->render('modals/reviews') ?>
        <?= $this->render('modals/success-order') ?>
        <?= $this->render('header') ?>
        <?= $content ?>
    </main>
    <?php $this->endBody() ?>
    <script defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBdDxXaKJJOG3mqd-ZKcBtoOoXN3BfFN8c&callback=initMap"></script>
    <script>
        <?php if($session->hasFlash('success_booking_order')): ?>
        <?php $session->getFlash('success_booking_order') ?>
        setTimeout(function () {
            modalInit('[data-modal="alert"]')
        }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>
<?php $this->endPage() ?>
