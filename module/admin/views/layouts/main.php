<?php
/**
 * Main layout view.
 */

use yii\helpers\Html;
use app\assets\AdminAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AdminAsset::register($this);
dmstr\web\AdminLteAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
$directoryAppAsset = Yii::$app->assetManager->getPublishedUrl('@web');

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
    <style>
        .select2-container .select2-selection--single .select2-selection__rendered {
            margin-top: 0;
        }
        .select2-container--krajee .select2-selection__clear {
            top: 0.1rem;
        }
       .admin-hotel-icons-container {
          display: flex;
          align-items: center;
          min-height: 50px;
       }
       .admin-hotel-icons-container .help-block {
          display: none;
       }
       .admin-hotel-icons-container .form-group, .admin-hotel-icons-container label {
          margin-bottom: 0;
       }
    </style>
</head>
<body class="hold-transition <?= \dmstr\helpers\AdminLteHelper::skinClass() ?> sidebar-mini">
<?php $this->beginBody() ?>
<div class="wrapper" id="app">
    <?= $this->render('header.php') ?>
    <?= $this->render(
        'left.php',
        ['directoryAsset' => $directoryAsset]
    )
    ?>
    <?= $this->render(
        'content.php',
        ['content' => $content, 'directoryAsset' => $directoryAsset]
    ) ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
