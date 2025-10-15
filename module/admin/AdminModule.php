<?php

namespace app\module\admin;

use Yii;
use app\module\admin\models\Module;
use yii\helpers\ArrayHelper;

class AdminModule extends \yii\base\Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'app\module\admin\controllers';
    /**
     * @var string
     */
    public $layout = 'main';

    /**
     * @return void
     */
    public function init(): void
    {
        Yii::$app->homeUrl = '/admin';
        Yii::$app->errorHandler->errorAction = 'admin/default/error';

        $this->modules = self::getChildModules();

        parent::init();
    }

    /**
     * @return array child modules.
     */
    public static function getChildModules(): array
    {
        $result = [];

        $modules = Module::find()->where(['status' => Module::STATUS_ACTIVE])->orderBy('sort_order ASC')->all();

        /** @var Module $module */
        foreach ($modules as $module) {
            $result[$module->name] = [
                'class' => "app\\module\\admin\\module\\$module->name\\" . ucfirst($module->name) . "Module",
            ];

        }

        return $result;
    }


    /**
     * @return array
     */
    public static function getMenuItems(): array
    {
        $basicItems = [
            ['label' => 'МЕНЮ', 'options' => ['class' => 'header']],
            ['label' => 'Панель состояния', 'icon' => 'dashboard', 'url' => ['/admin']],
            [
                'label' => 'Продукция',
                'icon' => 'tags',
                'items' => [
                    ['label' => 'Категории', 'icon' => 'sitemap', 'url' => ['/admin/product/category']],
                    ['label' => 'Товары', 'icon' => 'tag', 'url' => ['/admin/product/product']],
                    ['label' => 'Ингредиенты', 'icon' => 'flask', 'url' => ['/admin/product/ingredient']],
                    ['label' => 'Пицца "Классик"', 'icon' => 'star', 'url' => ['/admin/product/classic']],
                    ['label' => 'Настройки', 'icon' => 'cog', 'url' => ['/admin/product/customization']],
                ],
            ],
            ['label' => 'Заказы', 'icon' => 'shopping-cart', 'url' => ['/admin/order/order']],
            [
                'label' => 'Заведения доставки',
                'icon' => 'truck',
                'items' => [
                    ['label' => 'Населенные пункты', 'icon' => 'building', 'url' => ['/admin/order/city']],
                    ['label' => 'Заведения', 'icon' => 'coffee', 'url' => ['/admin/pizzeria/pizzeria']],
                ],
            ],
            [
                'label' => 'Рестораны',
                'icon' => 'cutlery',
                'items' => [
                    ['label' => 'Категории', 'icon' => 'sitemap', 'url' => ['/admin/restaurant-category']],
                    ['label' => 'Рестораны', 'icon' => 'cutlery', 'url' => ['/admin/restaurant']],
                ],
            ],
            ['label' => 'Страницы', 'icon' => 'newspaper-o', 'url' => ['/admin/page']],
            ['label' => 'Галереи', 'icon' => 'image', 'url' => ['/admin/gallery/album']],
            ['label' => 'Баннеры', 'icon' => 'columns', 'url' => ['/admin/banner']],
        ];

        $moduleItems = self::getModulesMenuItems();

        return ArrayHelper::merge($basicItems, $moduleItems, [
            [
                'label' => 'Вакансии',
                'icon' => 'thumb-tack',
                'url' => ['/admin/vacancy']
            ],
            [
                'label' => 'Отзывы',
                'icon' => 'comment',
                'items' => [
                    ['label' => 'Вакансии (заявки)', 'icon' => 'thumb-tack', 'url' => ['/admin/vacancy-requests']],
                    ['label' => 'Обратная связь', 'icon' => 'commenting-o', 'url' => ['/admin/feedback/feedback']],
                ],
            ],
            [
                'label' => 'Пользователи',
                'icon' => 'users',
                'items' => [
                    ['label' => 'Зарегистрированные', 'icon' => 'user', 'url' => ['/admin/user']],
                    ['label' => 'Подписчики', 'icon' => 'user-secret', 'url' => ['/admin/subscribers']],
                ],
                'url' => ['/admin/user']
            ],
            [
                'label' => 'PUSH и рассылка',
                'icon' => 'fas fa-bell',
                'items' => [
                    ['label' => 'PUSH-уведомления', 'icon' => 'fas fa-bell', 'url' => ['/admin/notifications-history']],
                    ['label' => 'Email-рассылка', 'icon' => 'fas fa-envelope', 'url' => ['/admin/mailing-history']],
                ],
            ],
            [
                'label' => 'Настройки',
                'icon' => 'cogs',
                'items' => [
                    ['label' => 'Общие', 'icon' => 'wrench', 'url' => ['/admin/setting']],
                    ['label' => 'Дизайн', 'icon' => 'paint-brush', 'url' => ['/admin/design']],
                    ['label' => 'Валюты', 'icon' => 'dollar', 'url' => ['/admin/currency/currency']],
                    ['label' => 'Языки', 'icon' => 'language', 'url' => ['/admin/language']],
                    ['label' => 'Переводы', 'icon' => 'globe', 'url' => ['/admin/source-message']],
                    ['label' => 'SEO URL', 'icon' => 'link', 'url' => ['/admin/seo-url']],
                ],
            ]
        ]);
    }

    /**
     * @return array
     */
    public static function getModulesMenuItems(): array
    {
        $items = [];
        $modules = Module::find()->where(['status' => Module::STATUS_ACTIVE])->orderBy('sort_order ASC')->all();
        /** @var Module $module */
        foreach ($modules as $module) {
            $moduleItems = self::getModuleMenuItems($module->name);
            if (isset($moduleItems['label'])) {
                $items[] = $moduleItems;
            } else {
                foreach ($moduleItems as $moduleItem) {
                    $items[] = $moduleItem;
                }
            }
        }
        return $items;
    }

    /**
     * @param string $moduleName
     * @return array
     */
    public static function getModuleMenuItems($moduleName): array
    {
        $moduleInfo = Module::getModuleInfo($moduleName);

        return $moduleInfo['menu'] ?? [];
    }
}
