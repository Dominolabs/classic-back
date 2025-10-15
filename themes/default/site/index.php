<?php

/* @var $page array */
/* @var $defaultPage array */
/* @var $menuItems array */
/* @var $this yii\web\View */

$this->title = $page['meta_title'] ? $page['meta_title'] :
    ($defaultPage['meta_title'] ? $defaultPage['meta_title'] : ($page['title'] ? $page['title'] : $defaultPage['title']));

$metaDescription = $page['meta_description'] ? $page['meta_description'] : $defaultPage['meta_description'];
$metaKeywords = $page['meta_keyword'] ? $page['meta_keyword'] : $defaultPage['meta_keyword'];

$this->params['metaDescription'] = $metaDescription;
$this->params['metaKeywords'] = $metaKeywords;

$this->params['backgroundImage'] = '/img/jpg/main-bg.jpg';
$contactsPageId = isset(Yii::$app->params['contactsPageId']) ? (int)Yii::$app->params['contactsPageId'] : null;
if (!empty($menuItems)): ?>
<div class="page-content__content  page-content__first-block">
    <ul class="sections-list" style="background-image: url('/img/jpg/mojo.jpg')">
        <?php foreach ($menuItems as $menuItem): ?>
        <?php if ((int)$menuItem['id'] !== $contactsPageId): ?>
        <li class="sections-list__item">
            <a href="<?= $menuItem['href'] ?>" class="sections-list__link">
                <svg>
                    <use xlink:href="./img/svg/sprite-logos.svg"></use>
                </svg>
            </a>
        </li>
        <?php endif ?>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>