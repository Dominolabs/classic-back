<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\module\admin\models\Language;
use app\assets\WebCamAsset;
use yii\helpers\Html;

WebCamAsset::register($this);

$languageId = Language::getLanguageIdByCode(Yii::$app->language);
$defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());

$titlePrefix = !empty(Yii::$app->params['titlePrefix'][$languageId]) ? Yii::$app->params['titlePrefix'][$languageId]
    : (!empty(Yii::$app->params['titlePrefix'][$defaultLanguageId]) ? Yii::$app->params['titlePrefix'][$defaultLanguageId] : '');
$titlePostfix = !empty(Yii::$app->params['titlePostfix'][$languageId]) ? Yii::$app->params['titlePostfix'][$languageId]
    : (!empty(Yii::$app->params['titlePostfix'][$defaultLanguageId]) ? Yii::$app->params['titlePostfix'][$defaultLanguageId] : '');


$this->title = $titlePrefix . Yii::t('webcam', 'KIDS ONLINE') . $titlePostfix;
?>
<?= $this->render('meta-tags', ['title' => $this->title]) ?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, shrink-to-fit=no" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
