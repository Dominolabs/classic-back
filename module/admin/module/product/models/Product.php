<?php

namespace app\module\admin\module\product\models;

use app\module\admin\models\PageDescription;
use app\module\admin\models\Restaurant;
use app\module\admin\models\SeoUrl;
use app\module\api\controllers\BaseApiController;
use app\module\api\module\viber\controllers\helpers\Helper;
use Imagine\Image\ManipulatorInterface;
use app\jobs\ImageCopiesJob;
use Yii;
use app\components\cart\CartPositionInterface;
use app\components\cart\CartPositionTrait;
use app\module\admin\module\currency\models\Currency;
use app\components\ImageBehavior;
use app\module\admin\models\Language;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;

/**
 * @property int $product_id
 * @property int $restaurant_id
 * @property int $weight_dish
 * @property string $weight
 * @property int $caloricity
 * @property string $image
 * @property string $price
 * @property string $price2
 * @property string $packaging_price
 * @property string $packaging_price2
 * @property int $is_promo
 * @property string $properties
 * @property int $status
 * @property bool $super_offer
 * @property int $sort_order
 * @property string $_badges
 * @property int $created_at
 * @property int $updated_at
 *
 * @property array $badges
 * @property int $badge
 * @property string $imageFile
 * @property string $restaurantTitle
 * @property Restaurant $restaurant
 * @property ProductDescription $productDescription
 * @property ProductDescription $productDescriptionDefaultLanguage
 * @property ProductToCategory $productCategory
 * @property Category $category
 * @property string $name
 * @property string $description
 * @property string $slug
 * @property string $pb_id
 * @property string $pb_big_id
 */
class Product extends ActiveRecord implements CartPositionInterface
{
    use CartPositionTrait;

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
        return 'tbl_product';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status', 'sort_order', 'pb_id'], 'required'],
            [['weight_dish', 'restaurant_id', 'caloricity', 'is_promo', 'sort_order', 'created_at', 'updated_at', 'badge'], 'integer'],
            [['price', 'price2', 'packaging_price', 'packaging_price2'], 'number'],
            [['image'], 'string', 'max' => 10000],
            [['properties'], 'string'],
            [['pb_id', 'pb_big_id'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            ['super_offer', 'default', 'value' => self::NO],
            ['super_offer', 'in', 'range' => [self::NO, self::YES]],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg', 'maxSize' => 1024 * 1024 * 10],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'product_id' => 'ID товара',
            'pb_id' => 'ID товара в ПриватБанке (для РРО)',
            'pb_big_id' => 'ID товара (большая пицца) в ПриватБанке (для РРО)',
            'restaurant_id' => 'Ресторан',
            'weight_dish' => 'Весовое блюдо',
            'caloricity' => 'Калорийность, ккал',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'price' => 'Цена',
            'price2' => 'Цена (Большая пицца)',
            'packaging_price' => 'Стоимость упаковки',
            'packaging_price2' => 'Стоимость упаковки (Большая пицца)',
            'properties' => 'Характеристики',
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'productName' => 'Название',
            'restaurantTitle' => 'Ресторан',
            'productWeight' => 'Размер порции',
            'super_offer' => 'Супер предложение',
            'productCategory' => 'Категория продукта',
            'categoryName' => 'Категория продукта',
            'badges' => 'Бейджи',
            'badge' => 'Бейджи',
        ];
    }

    public function fields(): array
    {
        $pizzaCategoryId = (int)Yii::$app->params['pizzaCategoryId'] ?: 0;
        $noodlesCategoryId = (int)Yii::$app->params['noodlesCategoryId'] ?: 0;

        return [
            'id' => 'product_id',
            'name',
            'restaurant_id',
            'description',
            'description_mobile_app' => static function ($product) {
                return html_entity_decode(strip_tags($product->description));
            },
            'category' => static function ($product) {
                return $product->productCategory->category ?? null;
            },
            'super_offer',
            'weight_dish',
            'weight',
            'caloricity',
            'image' => static function ($product) {
                if (!empty($product->image) && file_exists($product->getImagePath() . DIRECTORY_SEPARATOR . $product->image)) {
                    return BaseApiController::BASE_SITE_URL . 'image/product/' . $product->image;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'image_l' => static function ($model) {
                /** @var Product $model */
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_l.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/product/' . $model->image . '_l.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    return Helper::asset('image/product/' . $model->image);
                }

                return Helper::asset('image/placeholder.png');
            },
            'image_m' => static function ($model) {
                /** @var Product $model */
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_m.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/product/' . $model->image . '_m.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    return Helper::asset('image/product/' . $model->image);
                }

                return Helper::asset('image/placeholder.png');
            },
            'image_s' => static function ($model) {
                /** @var Product $model */
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_s.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/product/' . $model->image . '_s.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    return Helper::asset('image/product/' . $model->image);
                }

                return Helper::asset('image/placeholder.png');
            },
            'image_xs' => static function ($model) {
                /** @var Product $model */
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_xs.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/product/' . $model->image . '_xs.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    Yii::$app->queue->push(new ImageCopiesJob([
                        'file' => $model->getImagePath() . DIRECTORY_SEPARATOR . $model->image
                    ]));
                    return Helper::asset('image/product/' . $model->image);
                }
                $model->image = '';
                $model->save(false);

                return Helper::asset('image/placeholder.png');
            },
            'image_preview' => static function ($product) {
                if (!empty($product->image) && file_exists($product->getImagePath() . DIRECTORY_SEPARATOR . $product->image)) {
                    return BaseApiController::BASE_SITE_URL . trim($product->resizeImage($product->image, 300, 300), '/');
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'variants' => static function ($product) use ($pizzaCategoryId, $noodlesCategoryId) {
                $result = [];
                if (!empty((float)$product->price)) {
                    $result[] = [
                        'size' => 1,
                        'price' => (float)$product->price,
                        'name' => $product->productCategory->category_id === $pizzaCategoryId
                            ? Yii::t('product', 'Середня')
                            : ($product->productCategory->category_id === $noodlesCategoryId
                                ? Yii::t('product', 'Гостра')
                                : null
                            ),
                        'packaging_price' => (float)$product->packaging_price,
                    ];
                }
                if (!empty((float)$product->price2)) {
                    $result[] = [
                        'size' => 2,
                        'price' => (float)$product->price2,
                        'name' => $product->productCategory->category_id === $pizzaCategoryId
                            ? Yii::t('product', 'Велика')
                            : ($product->productCategory->category_id === $noodlesCategoryId
                                ? Yii::t('product', 'Не гостра')
                                : null
                            ),
                        'packaging_price' => (float)$product->packaging_price2,
                    ];
                }
                return $result;
            },
            'properties' => static function ($product) {
                $properties = json_decode($product->properties, true);
                $propertiesData = [];

                foreach ($properties as $property) {
                    $propertiesData[] = [
                        'id' => $property['id'],
                        'property' => $property['property'][Yii::$app->language] ?? ($property['property'][Yii::$app->urlManager->getDefaultLanguage()] ?? ''),
                        'sort_order' => $property['sort_order']
                    ];
                }

                return $propertiesData;
            },
            'badges' => static function ($product) {
                $badges = json_decode($product->_badges, true);
                $all = Yii::$app->params['productBadges'] ?? [];
                $result = [];
                if (!empty($badges) && is_array($badges)) {
                    foreach ($badges as $key) {
                        if (array_key_exists($key, $all)) {
                            $result[] = [
                                'id' => $key,
                                'name' => $all[$key]['name'][Language::getLanguageIdByCode(Yii::$app->language)] ?? ''
                            ];
                        }
                    }
                }
                return $result;
            },
            'sort_order',
            'created_at',
            'updated_at',
            'slug',
            'type' => static function () {
                return 'product';
            }
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
                'imageDirectory' => 'product',
            ]
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProductDescription(): ActiveQuery
    {
        return $this->hasOne(ProductDescription::class, ['product_id' => 'product_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductDescriptionDefaultLanguage(): ActiveQuery
    {
        return $this->hasOne(ProductDescription::class, ['product_id' => 'product_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())]);
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
        return $this->restaurant->restaurantTitle;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if (!empty($this->productDescription->name)) {
            return $this->productDescription->name;
        }

        if (!empty($this->productDescriptionDefaultLanguage->name)) {
            return $this->productDescriptionDefaultLanguage->name;
        }

        return '';
    }


    /**
     * @param $id
     * @return array|ActiveRecord|null
     */
    public static function getOneWithRestaurant($id)
    {
        return self::find()->where([self::tableName() . ".status" => self::STATUS_ACTIVE, 'product_id' => $id])->joinWith(['restaurant' => static function ($q) {
            $q->joinWith(['productCategories' => static function ($q) {
                $q->with('categoryDescription');
            }])->with('restaurantDescription');
        }])->with('productCategory')->asArray()->one();
    }


    /**
     * @return string
     */
    public function getDescription(): string
    {
        if (!empty($this->productDescription->description)) {
            return $this->productDescription->description;
        }

        if (!empty($this->productDescriptionDefaultLanguage->description)) {
            return $this->productDescriptionDefaultLanguage->description;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getWeight(): string
    {
        if (!empty($this->productDescription->weight)) {
            return $this->productDescription->weight;
        }

        if (!empty($this->productDescriptionDefaultLanguage->weight)) {
            return $this->productDescriptionDefaultLanguage->weight;
        }

        return '';
    }


    /**
     * @return ActiveQuery
     */
    public function getProductCategory(): ActiveQuery
    {
        return $this->hasOne(ProductToCategory::class, ['product_id' => 'product_id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['category_id' => 'category_id'])
            ->viaTable(ProductToCategory::tableName(), ['product_id' => 'product_id']);
    }

    /**
     * Returns product name.
     *
     * @return mixed product name
     */
    public function getProductName()
    {
        return $this->productDescription->name;
    }

    /**
     * @return string
     */
    public function getProductWeight()
    {
        return $this->productDescription->weight ?? '';
    }


    /**
     * @return int
     */
    public function getPrice(): int
    {
        if ((int)$this->getSize() === 1) {
            $price = $this->price;
        } else {
            $price = $this->price2;
        }

        if($this->isPizza()){
            if (!empty($this->getMainIngredients())) {
                foreach ($this->getMainIngredients() as $ingredient) {
                    if ($this->type !== 'classic') {
                        $price += $ingredient['price'] * $ingredient['quantity'];
                    }
                }
            }
            if (!empty($this->getAdditionalIngredients())) {
                foreach ($this->getAdditionalIngredients() as $ingredient) {
                    $price += $ingredient['price'] * $ingredient['quantity'];
                }
            }
        }

        return $price;
    }


    /**
     * Except of pizzas, some other products can have extra ingredients. So we need to add their value to total cost
     * but without multiplying their value by product quantity.
     * @return float|int
     */
    public function getIngredientsTotalValue ()
    {
        $value = 0;
        if (!empty($this->getMainIngredients())) {
            foreach ($this->getMainIngredients() as $ingredient) {
                    $value += $ingredient['price'] * $ingredient['quantity'];
            }
        }
        if (!empty($this->getAdditionalIngredients())) {
            foreach ($this->getAdditionalIngredients() as $ingredient) {
                $value += $ingredient['price'] * $ingredient['quantity'];
            }
        }
        return $value;
    }



    /**
     * @return bool
     */
    protected function isPizza(): bool
    {
        $pizzaCatId = Yii::$app->params['pizzaCategoryId'] ?? 0;
        if (
            (!empty($this->productCategory->category_id) && $this->productCategory->category_id === (int)$pizzaCatId)
            ||
            (!empty($this->productCategory->category->parent_id) && $this->productCategory->category->parent_id === (int)$pizzaCatId)
        ) {
            return true;
        }
        return false;
    }


    /**
     * Returns product id.
     * @return int product id
     */
    public function getId()
    {
        return $this->product_id . $this->size;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        $seoUrl = SeoUrl::find()->where('query = \'product_id=' . $this->product_id . '\'')->andWhere(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)])->one();

        if ($seoUrl) {
            return $seoUrl->keyword;
        }

        $seoUrl = SeoUrl::find()->where('query = \'product_id=' . $this->product_id . '\'')->andWhere(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())])->one();

        if (!empty($seoUrl)) {
            return $seoUrl->keyword;
        }

        return '';
    }

    /**
     * Returns product name by product id.
     *
     * @param int $productId product id
     * @return false|null|string product name
     */
    public static function getNameByProductId($productId)
    {
        return (new Query())
            ->select('(CASE WHEN pd.name != "" THEN pd.name ELSE pd2.name END) as name')
            ->distinct()
            ->from(self::tableName() . ' AS p')
            ->leftJoin(ProductDescription::tableName() . ' AS pd', 'p.product_id = pd.product_id AND pd.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(ProductDescription::tableName() . ' AS pd2', 'p.product_id = pd2.product_id AND pd2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where('p.product_id = ' . $productId . ' AND p.status = ' . self::STATUS_ACTIVE)
            ->scalar();
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
     * Returns original image path.
     *
     * @param string $filename image filename
     * @return string image path
     */
    public static function getOriginalImagePath($filename)
    {
        return (new self())->getImagePath() . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * Returns product URL.
     *
     * @param int $productId product id
     * @return false|null|string product URL
     */
    public static function getProductUrl($productId)
    {
        return Url::to(['/' . self::getProductCategorySeoUrl($productId) . '/' . self::getSeoUrl($productId)]);
    }

    /**
     * Returns category SEO URL for product.
     *
     * @param int $productId product id
     * @return bool category SEO URL for product, false if SEO URL not found
     */
    public static function getProductCategorySeoUrl($productId)
    {
        $path = array();

        $categories = (new Query())
            ->select('c.category_id, c.parent_id')
            ->from(ProductToCategory::tableName() . ' AS p2c')
            ->leftJoin(Category::tableName() . ' AS c', 'p2c.category_id = c.category_id')
            ->where('product_id = ' . $productId)
            ->all();

        foreach ($categories as $key => $category) {
            $categoryId = $category['category_id'];

            $path[$key] = self::getCategoryPath($categoryId);
        }

        if (count($path) > 0) {
            // Find largest path
            $result = array_map('strlen', $path);
            asort($result);
            $result = array_keys($result);

            $result = array_pop($result);

            return $path[$result];
        }

        return false;
    }

    /**
     * Returns category path.
     *
     * @param int $categoryId category id
     * @return string category path
     */
    public static function getCategoryPath($categoryId)
    {
        $path = '';

        $category = (new Query())
            ->select('*')
            ->from(Category::tableName())
            ->where(['category_id' => $categoryId])
            ->one();

        if (isset($category['parent_id']) && $category['parent_id'] != 0) {
            $path .= self::getCategoryPath($category['parent_id']) . '/';
        }

        $path .= Category::getSeoUrl($category['category_id']);

        return $path;
    }

    /**
     * Returns products data.
     *
     * @param array $data filter data
     * @param bool $indexByProductId true to index results by product id, otherwise - false. Defaults true.
     * @return array products data
     */
    public static function getProducts($data, $indexByProductId = true)
    {
        $result = [];

        if (!empty($data['filter_category_id'])) {
            $query = (new Query())
                ->select('p.product_id, c.packing_price')
                ->from(CategoryPath::tableName() . ' AS cp')
                ->leftJoin(ProductToCategory::tableName() . ' AS p2c', 'cp.category_id = p2c.category_id')
                ->leftJoin(Category::tableName() . ' AS c', 'c.category_id = cp.category_id')
                ->leftJoin(self::tableName() . ' AS p', 'p2c.product_id = p.product_id');
        } else {
            $query = (new Query())
                ->select('p.product_id')
                ->from(self::tableName() . ' AS p');
        }

        $query->leftJoin(ProductDescription::tableName() . ' AS pd', 'p.product_id = pd.product_id')
            ->where('pd.language_id = ' . Language::getLanguageIdByCode(Yii::$app->language) . ' AND p.status = ' . self::STATUS_ACTIVE);

        if (!empty($data['filter_category_id'])) {
            $query->andWhere('cp.path_id = ' . $data['filter_category_id']);
        }

        if (!empty($data['filter_name'])) {
            $implode = array();
            $sql = '';

            $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

            foreach ($words as $word) {
                $implode[] = "pd.name LIKE '%" . $word . "%'";
            }

            if ($implode) {
                $sql .= implode(" AND ", $implode) . "";
            }

            if (empty($data['search_by_name'])) {
                $sql .= " OR pd.description LIKE '%" . $data['filter_name'] . "%'";
            }

            $query->andWhere($sql);
        }

        if (!empty($data['filter_is_popular'])) {
            $query->andWhere('p.is_popular = ' . $data['filter_is_popular']);
        }

        $query->groupBy('p.product_id');

        if (!empty($data['sort']) && !empty($data['order'])) {
            $query->orderBy('p.' . $data['sort'] . ' ' . $data['order']);
        } else {
            $query->orderBy('p.sort_order');
        }

        if (!empty($data['limit'])) {
            $query->limit($data['limit']);
        }

        $products = $query->all();

        foreach ($products as $product) {
            if ($indexByProductId) {
                $result[$product['product_id']] = array_merge(self::getProduct($product['product_id']), [
                    'box_price' => $product['packing_price'] ?? 0
                ]);
            } else {
                $result[] = array_merge(self::getProduct($product['product_id']), [
                    'box_price' => $product['packing_price'] ?? 0
                ]);
            }
        }

        return $result;
    }

    /**
     * Returns product data.
     *
     * @param int $productId product id
     * @return array|bool product data
     */
    public static function getProduct($productId)
    {
        return (new Query())
            ->select('*, (CASE WHEN pd.name != "" THEN pd.name ELSE pd2.name END) as name, 
                (CASE WHEN pd.description != "" THEN pd.description ELSE pd2.description END) as description,
                (CASE WHEN pd.promo != "" THEN pd.promo ELSE pd2.promo END) as promo,
                p.image, (created_at > ' . strtotime('-20 day') . ') AS is_new,
                p.sort_order')
            ->distinct()
            ->from(self::tableName() . ' AS p')
            ->leftJoin(ProductDescription::tableName() . ' AS pd', 'p.product_id = pd.product_id AND pd.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(ProductDescription::tableName() . ' AS pd2', 'p.product_id = pd2.product_id AND pd2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where('p.product_id = ' . $productId . ' AND pd.language_id = ' . Language::getLanguageIdByCode(Yii::$app->language) . ' AND p.status = ' . self::STATUS_ACTIVE)
            ->one();
    }

    /**
     * Formats product price according to specified format.
     *
     * You can use the following tokens to format price:
     *
     * {sl}     - currency symbol left
     * {value}  - currency value
     * {sr}     - currency symbol right
     *
     * Any other strings will be outputs as is.
     *
     * @param float $price price value
     * @param string $currency currency code
     * @param bool|int $value currency value, if false default value will be used
     * @param string $format currency format, defaults '{sl}{value}{sr}'
     * @param string $thousandsSeparator price value thousands separator, defaults ' '
     * @return mixed|string currency string
     */
    public static function formatPrice($price, $currency, $value = false, $format = '{sl}{value}{sr}', $thousandsSeparator = ' ')
    {
        $currencies = Currency::getList();

        $symbolLeft = $currencies[$currency]['symbol_left'];
        $symbolRight = $currencies[$currency]['symbol_right'];
        $decimalPlace = $currencies[$currency]['decimal_place'];

        if (!$value) {
            $value = $currencies[$currency]['value'];
        }

        $amount = $value ? (float)$price * $value : (float)$price;

        $amount = round($amount, (int)$decimalPlace);

        $priceValue = number_format($amount, 0, '.', $thousandsSeparator);

        $string = $format;

        $string = str_replace('{sl}', $symbolLeft, $string);
        $string = str_replace('{value}', $priceValue, $string);
        $string = str_replace('{sr}', $symbolRight, $string);

        return $string;
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
     * @param int $status
     * @return array
     */
    public static function getList($status = self::STATUS_ACTIVE)
    {
        $list = Product::find()->where(['status' => $status])->all();
        $result = [''];

        foreach ($list as $item) {

            if (!empty($item['name']))$result[$item['product_id']] = $item['name'];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getBadges()
    {
        $all = Yii::$app->params['productBadges'];
        if (empty($this->_badges) || !is_array($all)) return [];
        $badges = json_decode($this->_badges, true);
        foreach ($badges as $key) {
            if (array_key_exists($key, $all)) $result[$key] = [
                'id' => $key,
                'name' => $all[$key]['name'][Language::getLanguageIdByCode(Yii::$app->language)] ?? ''
            ];
        }
        return $result ?? [];
    }


    /**
     * Returns page by language id.
     *
     * @param string $slug
     * @param int $languageId language id
     * @return array page
     */
    public static function getBySlug($slug)
    {
        return (new Query())
            ->select(['p.*', 'su.*'])
            ->from(self::tableName() . ' AS p')
            ->leftJoin(SeoUrl::tableName() . ' AS su', 'su.query = CONCAT(\'product_id=\', p.product_id)')
            ->where(['su.keyword' => $slug])
            ->one();
    }

    /**
     * @param array $arr
     */
    public function setBadges(array $arr)
    {
        if (!empty($arr)) {
            foreach ($arr as $key => $value) {
                if (is_int($value) || is_string($value) && ctype_digit($value)) {
                    if ($value == -1) continue;
                    $result[] = $value;
                } else
                    $result[] = array_key_exists('id', $value) ? $value['id'] : $key;
            }
        }
        $this->_badges = empty($result) ? null : json_encode($result);
    }

    /**
     * @return array
     */
    public static function getBadgesList()
    {
        $all = Yii::$app->params['productBadges'] ?? [];
        foreach ($all as $key => $item) {
            $result[$key] = $item['name'][Language::getLanguageIdByCode(Yii::$app->language)];
        }
        $result[-1] = 'Звичайний товар';
        return $result;
    }

    /**
     * @return int|mixed|string
     */
    public function getBadge()
    {
        if (empty($this->badges)) return -1;
        return (int)array_key_first($this->badges);
    }

    /**
     * @param $value
     */
    public function setBadge($value)
    {
        if ($value == -1) {
            $this->badges = [];
            return;
        }
        if (is_array($value) && array_key_exists('id', $value)) $value = $value['id'];
        $this->badges = [$value]; //array_merge($this->badges, [$value]);
    }
}
