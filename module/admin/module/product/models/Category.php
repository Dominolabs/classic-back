<?php

namespace app\module\admin\module\product\models;

use app\module\admin\models\Restaurant;
use app\module\api\controllers\BaseApiController;
use Yii;
use app\module\admin\models\SeoUrl;
use app\components\ImageBehavior;
use app\module\admin\models\Language;
use Imagine\Image\ManipulatorInterface;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\data\ArrayDataProvider;
use yii\data\Sort;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\Request;
use yii\web\UploadedFile;

/**
 * @property int $category_id
 * @property int $restaurant_id
 * @property string $image
 * @property float $packing_price
 * @property int $parent_id
 * @property int $top
 * @property int $contains_ingredients
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 *
 *  * @property string $restaurantTitle
 * @property Restaurant $restaurant
 * @property string $imageFile
 * @property CategoryDescription $categoryDescription
 * @property CategoryDescription $categoryDescriptionDefaultLanguage
 * @property string $name
 * @property string $categoryName
 * @property string $description
 * @property string $categoryMetaTitle
 * @property string $categoryMetaDescription
 * @property string $categoryMetaKeyword
 * @property string $slug
 * @property $topProducts
 */
class Category extends ActiveRecord
{
    public const STATUS_NOT_ACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public const NO = 0;
    public const YES = 1;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'tbl_category';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['packing_price', 'default', 'value' => 0],
            [['top', 'contains_ingredients', 'status', 'sort_order'], 'required'],
            [['parent_id', 'restaurant_id', 'top', 'contains_ingredients', 'status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['image'], 'string', 'max' => 255],
            [['packing_price'], 'number'],
            ['top', 'default', 'value' => self::YES],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            [
                ['imageFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg, gif, svg',
                'maxSize' => 1024 * 1024 * 10
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'category_id' => 'ID',
            'restaurant_id' => 'Ресторан',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'packing_price' => 'Стоимость упаковки, грн',
            'parent_id' => 'Родительская категория',
            'top' => 'Показывать в главном меню',
            'contains_ingredients' => 'Содержит ингредиенты',
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'categoryName' => 'Категория',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            'image' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'category',
            ]
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id' => 'category_id',
            'image' => static function($category) {
                if (!empty($category->image) && file_exists($category->getImagePath() . DIRECTORY_SEPARATOR . $category->image)) {
                    return BaseApiController::BASE_SITE_URL . 'image/category/' . $category->image;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'image_preview' => static function($category) {
                if (!empty($category->image) && file_exists($category->getImagePath() . DIRECTORY_SEPARATOR . $category->image)) {
                    return BaseApiController::BASE_SITE_URL . trim($category->resizeImage($category->image, 300, 300), '/');
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'name',
            'description',
            'meta_title' => 'categoryMetaTitle',
            'meta_description' => 'categoryMetaDescription',
            'meta_keyword' => 'categoryMetaKeyword',
            'packing_price',
            'parent_id',
            'created_at',
            'updated_at',
            'slug',
            'has_ingredients' => static function ($category) {
                return (bool) count($category->ingredients);
            },
            'ingredients'
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCategoryDescription(): ActiveQuery
    {
        return $this->hasOne(CategoryDescription::class, ['category_id' => 'category_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategoryDescriptionDefaultLanguage(): ActiveQuery
    {
        return $this->hasOne(CategoryDescription::class, ['category_id' => 'category_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())]);
    }


    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getVariantsProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['product_id' => 'product_id'])
            ->andOnCondition('price2 != 0')
            ->viaTable('tbl_product_to_category', ['category_id' => 'category_id']);
    }


    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getNoVariantsProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['product_id' => 'product_id'])
            ->andOnCondition('price2 = 0')
            ->viaTable('tbl_product_to_category', ['category_id' => 'category_id']);
    }



    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['product_id' => 'product_id'])
            ->viaTable('tbl_product_to_category', ['category_id' => 'category_id']);
    }





    /**
     * ActiveRelation to CategoryPath model.
     *
     * @return ActiveQuery active query instance
     */
    public function getCategoryPath()
    {
        return $this->hasMany(CategoryPath::class, ['category_id' => 'category_id']);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if (!empty($this->categoryDescription->name)) {
            return $this->categoryDescription->name;
        }

        if (!empty($this->categoryDescriptionDefaultLanguage->name)) {
            return $this->categoryDescriptionDefaultLanguage->name;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if (!empty($this->categoryDescription->description)) {
            return $this->categoryDescription->description;
        }

        if (!empty($this->categoryDescriptionDefaultLanguage->description)) {
            return $this->categoryDescriptionDefaultLanguage->description;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCategoryMetaTitle(): string
    {
        if (!empty($this->categoryDescription->meta_title)) {
            return $this->categoryDescription->meta_title;
        }

        if (!empty($this->categoryDescriptionDefaultLanguage->meta_title)) {
            return $this->categoryDescriptionDefaultLanguage->meta_title;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCategoryMetaDescription(): string
    {
        if (!empty($this->categoryDescription->meta_description)) {
            return $this->categoryDescription->meta_description;
        }

        if (!empty($this->categoryDescriptionDefaultLanguage->meta_description)) {
            return $this->categoryDescriptionDefaultLanguage->meta_description;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getCategoryMetaKeyword(): string
    {
        if (!empty($this->categoryDescription->meta_keyword)) {
            return $this->categoryDescription->meta_keyword;
        }

        if (!empty($this->categoryDescriptionDefaultLanguage->meta_keyword)) {
            return $this->categoryDescriptionDefaultLanguage->meta_keyword;
        }

        return '';
    }

    /**
     * Returns category name.
     *
     * ToDo: find better solution.
     *
     * @return mixed category name
     */
    public function getCategoryName()
    {
        $categoryName = $this->categoryDescription->name;

        $categoryPathName = (new Query())
            ->select('GROUP_CONCAT(cd.name ORDER BY level SEPARATOR " > ")')
            ->from(CategoryPath::tableName() . ' AS cp')
            ->leftJoin(CategoryDescription::tableName() . ' AS cd',
                'cp.path_id = cd.category_id AND cp.category_id != cp.path_id')
            ->where('cp.category_id = ' . $this->category_id . ' AND cd.language_id = ' . Language::getLanguageIdByCode(Yii::$app->language))
            ->groupBy('cp.category_id')
            ->scalar();

        return !empty($categoryPathName) ? ($categoryPathName . ' > ' . $categoryName) : $categoryName;
    }

    /**
     * @return ActiveQuery
     */
    public function getRestaurant()
    {
        return $this->hasOne(Restaurant::class, ['restaurant_id' => 'restaurant_id']);
    }

    /**
     * @return string
     */
    public function getRestaurantTitle()
    {
        return $this->restaurant->restaurantTitle ?? '';
    }

    /**
     * Saves category path using MySQL Hierarchical Data Closure Table Pattern.
     *
     * @throws \yii\db\Exception if db queries execution failed
     */
    public function saveCategoryPath()
    {
        // MySQL Hierarchical Data Closure Table Pattern
        $level = 0;

        $categoryPaths = CategoryPath::find()->where(['category_id' => $this->parent_id])->orderBy('level ASC')->all();

        /** @var CategoryPath $categoryPath */
        foreach ($categoryPaths as $categoryPath) {
            Yii::$app->db->createCommand()->insert('{{%category_path}}', [
                'category_id' => $this->category_id,
                'path_id' => $categoryPath->path_id,
                'level' => $level,
            ])->execute();

            $level++;
        }

        Yii::$app->db->createCommand()->insert('{{%category_path}}', [
            'category_id' => $this->category_id,
            'path_id' => $this->category_id,
            'level' => $level,
        ])->execute();
    }

    /**
     * Updates category path using MySQL Hierarchical Data Closure Table Pattern.
     *
     * @throws \yii\db\Exception if db queries execution failed
     */
    public function updateCategoryPath()
    {
        // MySQL Hierarchical Data Closure Table Pattern

        $categoryPaths = CategoryPath::find()->where(['path_id' => $this->category_id])->orderBy('level ASC')->all();

        if (!empty($categoryPaths)) {
            /** @var CategoryPath $categoryPath */
            foreach ($categoryPaths as $categoryPath) {
                // Delete the path below the current one
                Yii::$app->db->createCommand()->delete('{{%category_path}}',
                    'category_id = ' . $categoryPath->category_id . ' AND level < ' . $categoryPath->level)->execute();

                $path = [];

                // Get the nodes new parents
                $parents = CategoryPath::find()->where(['category_id' => $this->parent_id])->orderBy('level ASC')->all();

                /** @var CategoryPath $parent */
                foreach ($parents as $parent) {
                    $path[] = $parent->path_id;
                }

                // Get whats left of the nodes current path
                $leftPaths = CategoryPath::find()->where(['category_id' => $categoryPath->category_id])->orderBy('level ASC')->all();

                /** @var CategoryPath $leftPath */
                foreach ($leftPaths as $leftPath) {
                    $path[] = $leftPath->path_id;
                }

                // Combine the paths with a new level
                $level = 0;

                foreach ($path as $pathId) {
                    $params = [
                        ':category_id' => $categoryPath->category_id,
                        ':path_id' => $pathId,
                        ':level' => $level,
                    ];

                    Yii::$app->db->createCommand('REPLACE INTO ' . CategoryPath::tableName() . ' SET category_id = :category_id, path_id = :path_id, level = :level')
                        ->bindValues($params)
                        ->execute();

                    $level++;
                }
            }
        } else {
            // Delete the path below the current one
            Yii::$app->db->createCommand()->delete('{{%category_path}}',
                ['category_id' => $this->category_id])->execute();

            // Fix for records with no paths
            $level = 0;

            $paths = CategoryPath::find()->where(['category_id' => $this->parent_id])->orderBy('level ASC')->all();

            /** @var CategoryPath $path */
            foreach ($paths as $path) {
                Yii::$app->db->createCommand()->insert('{{%category_path}}', [
                    'category_id' => $this->category_id,
                    'path_id' => $path->path_id,
                    'level' => $level,
                ])->execute();

                $level++;
            }

            $params = [
                ':category_id' => $this->category_id,
                ':path_id' => $this->category_id,
                ':level' => $level,
            ];

            Yii::$app->db->createCommand('REPLACE INTO ' . CategoryPath::tableName() . ' SET category_id = :category_id, path_id = :path_id, level = :level')
                ->bindValues($params)
                ->execute();
        }
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        $seoUrl = SeoUrl::find()->where('query = \'category_id=' . $this->category_id . '\'')->andWhere(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)])->one();

        if ($seoUrl) {
            return $seoUrl->keyword;
        }

        $seoUrl = SeoUrl::find()->where('query = \'category_id=' . $this->category_id . '\'')->andWhere(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())])->one();

        if (!empty($seoUrl)) {
            return $seoUrl->keyword;
        }

        return '';
    }

    /**
     * Returns statuses list.
     *
     * @return array statuses list data
     */
    public static function getStatusesList()
    {
        return [
            self::STATUS_ACTIVE => 'Включено',
            self::STATUS_NOT_ACTIVE => 'Отключено'
        ];
    }

    /**
     * Returns status name by specified status constant.
     *
     * @param integer $status status constant
     * @return mixed|string status name
     */
    public static function getStatusName($status)
    {
        $statuses = self::getStatusesList();
        return isset($statuses[$status]) ? $statuses[$status] : 'Неопределено';
    }

    /**
     * @param string $filename image filename
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @param int $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100). Defaults 100.
     * @return null|string image URL
     */
    public static function getImageUrl($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_INSET, $quality = 100)
    {
        return (new self())->resizeImage($filename, $width, $height, $mode, $quality);
    }

    /**
     * Returns categories list data.
     *
     * @param null|int $categoryId category id
     * @param null $containsIngredients
     * @return array categories list data
     */
    public static function getList($categoryId = null, $containsIngredients = null)
    {
        $result = [];

        $sql = self::find();

        if ($categoryId !== null) {
            $sql = $sql->where('category_id != ' . $categoryId);
        }

        if ($containsIngredients) {
            $sql = $sql->andWhere('contains_ingredients = ' . $containsIngredients);
        }

        $categories = $sql->orderBy('sort_order ASC')->all();

        /** @var Category $category */
        foreach ($categories as $category) {
            $result[$category->category_id] = $category->getCategoryName() .' ('. $category->getRestaurantTitle().')';
        }

        return $result;
    }


    /**
     * @return ActiveQuery
     */
    public function getIngredients(): ActiveQuery
    {
        return $this->hasMany(Ingredient::class, ['category_id' => 'category_id'])
            ->joinWith('ingredientDescription');
    }


    /**
     * Returns categories list.
     *
     * @param int $parentId parent category id
     * @return array categories list
     */
    public static function getCategories($parentId = 0)
    {
        return (new Query())->select('c.*, cd.name AS name')
            ->from(self::tableName() . ' AS c')
            ->leftJoin(CategoryDescription::tableName() . ' AS cd', 'c.category_id = cd.category_id')
            ->where([
                'c.parent_id' => $parentId,
                'cd.language_id' => Language::getLanguageIdByCode(Yii::$app->language),
                'c.status' => Category::STATUS_ACTIVE,
            ])
            ->orderBy('c.sort_order ASC, LCASE(cd.name) ASC')
            ->all();
    }

    /**
     * Returns category data.
     *
     * @param int $categoryId category id
     * @return array|bool category data
     */
    public static function getCategory($categoryId)
    {
        return (new Query())->select('c.*, (CASE WHEN cd.name != "" THEN cd.name ELSE cd2.name END) as name,
                (CASE WHEN cd.meta_title != "" THEN cd.meta_title ELSE cd2.meta_title END) as meta_title,
                (CASE WHEN cd.meta_description != "" THEN cd.meta_description ELSE cd2.meta_description END) as meta_description,
                (CASE WHEN cd.meta_keyword != "" THEN cd.meta_keyword ELSE cd2.meta_keyword END) as meta_keyword,
            ')
            ->distinct()
            ->from(self::tableName() . ' AS c')
            ->leftJoin(CategoryDescription::tableName() . ' AS cd',
                'c.category_id = cd.category_id AND cd.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(CategoryDescription::tableName() . ' AS cd2',
                'c.category_id = cd2.category_id AND cd2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where([
                'c.category_id' => $categoryId,
                'c.status' => Category::STATUS_ACTIVE,
            ])
            ->one();
    }

    /**
     * Returns category URL.
     *
     * @param int $categoryId category id
     * @return false|null|string category URL
     */
    public static function getUrl($categoryId)
    {
        return Url::to(['/' . self::getPath($categoryId)]);
    }

    /**
     * Returns category path.
     *
     * @param int $categoryId category id
     * @return string category path
     */
    public static function getPath($categoryId)
    {
        $path = '';

        $category = (new Query())
            ->select('')
            ->from(Category::tableName())
            ->where(['category_id' => $categoryId])
            ->one();

        if ($category['parent_id'] != 0) {
            $path .= self::getPath($category['parent_id']) . '/';
        }

        $path .= self::getSeoUrl($category['category_id']);

        return $path;
    }

    /**
     * Returns category SEO URL.
     *
     * @param int $categoryId category id
     * @return false|null|string category SEO URL
     */
    public static function getSeoUrl($categoryId)
    {
        return (new Query())
            ->select('keyword')
            ->from(SeoUrl::tableName())
            ->where([
                'query' => 'category_id=' . $categoryId,
                'language_id' => Language::getLanguageIdByCode(Yii::$app->language),
            ])
            ->scalar();
    }

    /**
     * Returns sorts.
     *
     * @param Sort $sort sort object
     * @return array sorts
     * @throws \yii\base\InvalidConfigException if the sort attribute is unknown
     */
    public static function getSorts($sort)
    {
        return [
            'sort_order' => [
                'label' => Yii::t('product', 'По умолчанию'),
                'url' => static::createSortUrl($sort, 'sort_order', SORT_ASC, true),
            ],
            '-created_at' => [
                'label' => Yii::t('product', 'Сначала новее'),
                'url' => static::createSortUrl($sort, 'created_at', SORT_DESC, true),
            ],
            'created_at' => [
                'label' => Yii::t('product', 'Сначала старее'),
                'url' => static::createSortUrl($sort, 'created_at', SORT_ASC, true),
            ],
            '-price' => [
                'label' => Yii::t('product', 'Сначала дороже'),
                'url' => static::createSortUrl($sort, 'price', SORT_DESC, true),
            ],
            'price' => [
                'label' => Yii::t('product', 'Сначала дешевле'),
                'url' => static::createSortUrl($sort, 'price', SORT_ASC, true),
            ]
        ];
    }

    /**
     * Returns sort params list.
     *
     * @return array sort params
     */
    public static function getSortParams()
    {
        return [
            [
                'name' => Yii::t('product', 'По умолчанию'),
                'key' => 'sort_order'
            ],
            [
                'name' => Yii::t('product', 'Сначала новее'),
                'key' => '-created_at'
            ],
            [
                'name' => Yii::t('product', 'Сначала старее'),
                'key' => 'created_at'
            ],
            [
                'name' => Yii::t('product', 'Сначала дороже'),
                'key' => '-price'
            ],
            [
                'name' => Yii::t('product', 'Сначала дешевле'),
                'key' => 'price'
            ]
        ];
    }

    /**
     * Returns current sort name.
     *
     * @param ArrayDataProvider $dataProvider data provider
     * @return string current sort name
     * @throws InvalidConfigException if the sort attribute is unknown
     */
    public static function getCurrentSort($dataProvider)
    {
        $sorts = self::getSorts($dataProvider->getSort());

        $sortParam = Yii::$app->request->getQueryParam('sort');

        if (isset($sorts[$sortParam])) {
            return $sorts[$sortParam]['label'];
        }

        return Yii::t('product', 'По умолчанию');
    }

    /**
     * Returns category path.
     * This is QueryBuilder version.
     *
     * @param int $categoryId category id
     * @return array category path data
     */
    public static function getCategoryPathQB($categoryId)
    {
        return (new Query())
            ->select('*')
            ->from(CategoryPath::tableName())
            ->where([
                'category_id' => $categoryId,
            ])
            ->all();
    }

    /**
     * Creates a URL for sorting the data by the specified attribute.
     *
     * @param Sort $sort sort object instance
     * @param string $attribute the attribute name
     * @param int $direction sort direction
     * @param bool $absolute whether to create an absolute URL. Defaults to `false`.
     * @return string the URL for sorting. False if the attribute is invalid.
     * @throws InvalidConfigException if the attribute is unknown
     */
    protected static function createSortUrl($sort, $attribute, $direction = SORT_ASC, $absolute = false)
    {
        if (($params = $sort->params) === null) {
            $request = Yii::$app->getRequest();
            $params = $request instanceof Request ? $request->getQueryParams() : [];
        }

        foreach ($params as $key => $param) {
            if ($key == 'count' || $key == 'price' || $key == 'withContent' || $key == 'curRoute' || $key == 'route') {
                unset($params[$key]);
            }
        }

        $params[$sort->sortParam] = self::createSortParam($sort, $attribute, $direction);

        if (isset($params['path'])) {
            $params[0] = ltrim($params['path'], '/');
            unset($params['path']);
        } else {
            $params[0] = $sort->route === null ? Yii::$app->controller->getRoute() : $sort->route;
        }

        $urlManager = $sort->urlManager === null ? Yii::$app->getUrlManager() : $sort->urlManager;

        if ($absolute) {
            return $urlManager->createAbsoluteUrl($params);
        }

        return $urlManager->createUrl($params);
    }

    /**
     * Creates the sort variable for the specified attribute.
     * The newly created sort variable can be used to create a URL that will lead to
     * sorting by the specified attribute.
     *
     * @param Sort $sort sort object instance
     * @param string $attribute the attribute name
     * @param int $direction sort direction
     * @return string the value of the sort variable
     * @throws InvalidConfigException if the specified attribute is not defined in sort attributes
     */
    protected static function createSortParam($sort, $attribute, $direction = SORT_ASC)
    {
        if (!isset($sort->attributes[$attribute])) {
            throw new InvalidConfigException("Unknown attribute: $attribute");
        }

        $directions = $sort->getAttributeOrders();

        if (isset($directions[$attribute])) {
            unset($directions[$attribute]);
        }

        if ($sort->enableMultiSort) {
            $directions = array_merge([$attribute => $direction], $directions);
        } else {
            $directions = [$attribute => $direction];
        }

        $sorts = [];

        foreach ($directions as $attribute => $direction) {
            $sorts[] = $direction === SORT_DESC ? '-' . $attribute : $attribute;
        }

        return implode($sort->separator, $sorts);
    }

    /**
     * Returns all models count.
     *
     * @return int|string models count
     */
    public static function getAllCount()
    {
        return self::find()->count();
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getTopProducts()
    {
        return $this->hasMany(Product::class, ['product_id' => 'product_id'])
            ->viaTable('{{%category_top_products}}', ['category_id' => 'category_id']);
    }
}
