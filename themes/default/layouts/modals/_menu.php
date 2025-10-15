<?php

use app\module\admin\models\Language;
use app\module\admin\models\Page;
use yii\helpers\Url;

/* @var $this \yii\web\View */

$languageId = Language::getLanguageIdByCode(Yii::$app->language);
$defaultLanguageId = Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage());

$menuItems = Page::getMenuItems($languageId);

if (empty($menuItems)) {
    $menuItems = Page::getMenuItems($defaultLanguageId);
}

// Remove Main page menu item
array_shift($menuItems);

$contactsPageId = isset(Yii::$app->params['contactsPageId']) ? (int)Yii::$app->params['contactsPageId'] : null;
$isHomePage = Page::isHomePage();

if (!empty($menuItems)): ?>
<nav class="side-nav__main-menu  main-menu">
    <?php foreach ($menuItems as $menuItem): ?>
        <?php if (!$isHomePage || (int)$menuItem['id'] === $contactsPageId): ?>
        <a href="<?= $menuItem['href'] ?>" class="main-menu__link<?= ($menuItem['active']) ? ' main-menu__link--active' : '' ?>">
            <span class="main-menu__link--text"><?= $menuItem['title'] ?></span>
        </a>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if (!$isHomePage): ?>
    <a href="<?= Url::to(['webcam']) ?>" class="main-menu__link">
        <span class="main-menu__link--text"><?= Yii::t('webcam', 'KIDS ONLINE') ?></span>
    </a>
    <?php endif; ?>
</nav>
<?php endif; ?>
