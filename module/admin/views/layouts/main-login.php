<?php
/**
 * Main login layout view.
 */

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

dmstr\web\AdminLteAsset::register($this);

$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '180x180', 'href' => '/favicon/apple-touch-icon.png']);
$this->registerLinkTag(['rel' => 'icon', 'type' => "image/png", 'sizes' => '32x32', 'href' => '/favicon/favicon-32x32.png']);
$this->registerLinkTag(['rel' => 'icon', 'type' => "image/png", 'sizes' => '16x16', 'href' => '/favicon/favicon-16x16.png']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="login-page">
<?php $this->beginBody() ?>
    <?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
