<?php

namespace app\module\admin\models;

use app\components\SitemapBehavior;
use app\components\ImageBehavior;
use app\module\admin\module\gallery\models\Album;
use app\module\admin\module\product\models\Category;
use app\module\api\controllers\BaseApiController;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * @property int $restaurant_id
 * @property int $restaurant_category_id
 * @property string $image
 * @property string $image_transparent
 * @property string $background_image
 * @property integer $top_banner_id
 * @property integer $gallery_id
 * @property integer $menu_banner_id
 * @property string $facebook
 * @property string $instagram
 * @property string $youtube
 * @property string $vk
 * @property int $online_delivery
 * @property int $online_delivery_orders_processing
 * @property int $self_picking
 * @property int $status
 * @property int $sort_order
 * @property int $classic_status
 * @property int $lat
 * @property int $long
 * @property int $created_at
 * @property int $updated_at
 *
 * @property string $imageFile
 * @property string $restaurantTitle
 * @property string $restaurantDescription1
 * @property string $restaurantDescription2
 * @property string $restaurantSchedule
 * @property string $restaurantPhone
 * @property string $restaurantAddress
 * @property string $restaurantGmap
 * @property string $restaurantMetaTitle
 * @property string $restaurantMetaDescription
 * @property string $restaurantMetaKeyword
 * @property RestaurantDescription $restaurantDescription
 * @property RestaurantDescription $restaurantDescriptionDefaultLanguage
 * @property Banner $restaurantTopBanner
 * @property Album $restaurantGallery
 * @property Banner $restaurantMenuBanner
 *
 * @property RestaurantCategory|null $restaurantCategory
 * @property string $slug
 */
class Restaurant extends ActiveRecord
{
    public const STATUS_NOT_ACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public const YES = 1;
    public const NO = 0;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @var UploadedFile
     */
    public $imageTransparentFile;


    /**
     * @var UploadedFile
     */
    public $backgroundImageFile;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%restaurant}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status', 'sort_order', 'online_delivery'], 'required'],
            [['restaurant_category_id', 'online_delivery', 'online_delivery_orders_processing', 'self_picking', 'status', 'top_banner_id', 'gallery_id', 'menu_banner_id', 'sort_order'], 'integer'],
            [['image', 'image_transparent', 'background_image', 'facebook', 'instagram', 'youtube', 'vk'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            ['online_delivery', 'default', 'value' => self::NO],
            ['online_delivery', 'in', 'range' => [self::YES, self::NO]],
            [['lat', 'long'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['imageFile','imageTransparentFile', 'backgroundImageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg',
                'maxSize' => 1024 * 1024 * 10, 'checkExtensionByMimeType' => false
            ],
        ];
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return [
            'id' => 'restaurant_id',
            'restaurant_category_id' => 'restaurant_category_id',
            'title' => 'restaurantTitle',
            'description1' => 'restaurantDescription1',
            'description2' => 'restaurantDescription2',
            'schedule' => 'restaurantSchedule',
            'phone' => 'restaurantPhone',
            'address' => 'restaurantAddress',
            'gmap' => 'restaurantGmap',
            'lat',
            'long',
            'meta_title' => 'restaurantMetaTitle',
            'meta_description' => 'restaurantMetaDescription',
            'meta_keyword' => 'restaurantMetaKeyword',
            'image' => static function ($restaurant) {
                if (!empty($restaurant->image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image)) {
                    return BaseApiController::BASE_SITE_URL . 'image/restaurant/' . $restaurant->image;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'image_preview' => static function ($restaurant) {
                if (!empty($restaurant->image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image)) {
                    return BaseApiController::BASE_SITE_URL . trim($restaurant->resizeImage($restaurant->image, 300, 300), '/');
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'image_transparent' => static function ($restaurant) {
                if (!empty($restaurant->image_transparent) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image_transparent)) {
                    return BaseApiController::BASE_SITE_URL . 'image/restaurant/' . $restaurant->image_transparent;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'image_transparent_preview' => static function ($restaurant) {
                if (!empty($restaurant->image_transparent) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image_transparent)) {
                    return BaseApiController::BASE_SITE_URL . trim($restaurant->resizeImage($restaurant->image_transparent, 300, 300), '/');
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'background_image' => static function ($restaurant) {
                if (!empty($restaurant->background_image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->background_image)) {
                    return BaseApiController::BASE_SITE_URL . 'image/restaurant/' . $restaurant->background_image;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'background_image_preview' => static function ($restaurant) {
                if (!empty($restaurant->background_image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->background_image)) {
                    return BaseApiController::BASE_SITE_URL . trim($restaurant->resizeImage($restaurant->background_image, 300, 300), '/');
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'facebook',
            'instagram',
            'youtube',
            'vk',
            'online_delivery',
            'online_delivery_orders_processing',
            'self_picking',
            'sort_order',
            'created_at',
            'classic_status' => static function () {
                $classicModel = Classic::find()->one();
                return $classicModel ? $classicModel->status : null;
            },
            'updated_at',
            'slug',
            'top_banner' => static function ($restaurant) {
                return $restaurant->restaurantTopBanner;
            },
            'gallery' => static function ($restaurant) {
                return $restaurant->restaurantGallery;
            },
            'menu_banner' => static function ($restaurant) {
                return $restaurant->restaurantMenuBanner;
            },
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'restaurant_id' => 'ID страницы',
            'restaurant_category_id' => 'Категория',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'image_transparent' => 'Фоновое полупрозрачное изображение',
            'imageTransparentFile' => 'Фоновое полупрозрачное изображение',
            'background_image' => 'Фоновое изображение',
            'backgroundImageFile' => 'Фоновое изображение',
            'top_banner_id' => 'Баннер в шапке',
            'gallery_id' => 'Фотогалерея',
            'classic_status' => 'Статус піци конструктор',
            'menu_banner_id' => 'Баннер меню',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'vk' => 'VK',
            'online_delivery' => 'Онлайн доставка',
            'online_delivery_orders_processing' => "Онлайн доставка\r\n(обработка заказов)",
            'self_picking' => 'Самовывоз',
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'restaurantName' => 'Название',
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
                'imageDirectory' => 'restaurant',
            ],
            'imageTransparent' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'restaurant',
            ],
            'backgroundImage' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'restaurant',
            ],
            'sitemap' => [
                'class' => SitemapBehavior::class,
                'query' => 'restaurant_id',
                'scope' => function ($model) {
                    /** @var ActiveQuery $model */
                    $model->andWhere(['status' => Restaurant::STATUS_ACTIVE]);
                },
                'dataClosure' => function ($model, $seoUrl) {
                    /** @var self $model */
                    /** @var string $seoUrl */
                    return [
                        'loc' => Restaurant::getUrl($seoUrl),
                        'lastmod' => $model->updated_at,
                        'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                        'priority' => ($seoUrl === '/') ? 1 : 0.8
                    ];
                }
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getRestaurantDescription(): ActiveQuery
    {
        return $this->hasOne(RestaurantDescription::class, ['restaurant_id' => 'restaurant_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * @return ActiveQuery active query instance
     */
    public function getRestaurantDescriptionDefaultLanguage(): ActiveQuery
    {
        return $this->hasOne(RestaurantDescription::class, ['restaurant_id' => 'restaurant_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())]);
    }

    public function getClassicStatus()
    {
        $classicModel = Classic::find()->one();
        return $classicModel ? $classicModel->status : null;
    }


    /**
     * @return ActiveQuery
     */
    public function getRestaurantTopBanner(): ActiveQuery
    {
        return $this->hasOne(Banner::class, ['banner_id' => 'top_banner_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRestaurantMenuBanner(): ActiveQuery
    {
        return $this->hasOne(Banner::class, ['banner_id' => 'menu_banner_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRestaurantGallery(): ActiveQuery
    {
        return $this->hasOne(Album::class, ['album_id' => 'gallery_id']);
    }

    /**
     * @return string
     */
    public function getRestaurantTitle(): string
    {
        if (!empty($this->restaurantDescription->title)) {
            return $this->restaurantDescription->title;
        }

        if (!empty($this->restaurantDescriptionDefaultLanguage->title)) {
            return $this->restaurantDescriptionDefaultLanguage->title;
        }

        return '';
    }


    public function getRestaurantTitleWithAddress(): string
    {
        if ($this->restaurantCategory) {
            if (!empty($this->restaurantCategory->getRestaurantCategoryName() && !empty($this->restaurantDescription->address))) {
                return $this->restaurantCategory->getRestaurantCategoryName() . ' (' . $this->restaurantDescription->address . ')';
            }

            if (!empty($this->restaurantDescriptionDefaultLanguage->title) && !empty($this->restaurantDescriptionDefaultLanguage->address)) {
                return $this->restaurantDescriptionDefaultLanguage->title . ' (' . $this->restaurantDescriptionDefaultLanguage->address . ')';
            }
        } else {
            if (!empty($this->restaurantDescription->title) && !empty($this->restaurantDescription->address)) {
                return $this->restaurantDescription->title . ' (' . $this->restaurantDescription->address . ')';
            }

            if (!empty($this->restaurantDescriptionDefaultLanguage->title) && !empty($this->restaurantDescriptionDefaultLanguage->address)) {
                return $this->restaurantDescriptionDefaultLanguage->title . ' (' . $this->restaurantDescriptionDefaultLanguage->address . ')';
            }
        }
        return '';
    }

    /**
     * @return string
     */
    public function getRestaurantDescription1(): string
    {
        if (!empty($this->restaurantDescription->description1)) {
            return $this->restaurantDescription->description1;
        }

        if (!empty($this->restaurantDescriptionDefaultLanguage->description1)) {
            return $this->restaurantDescriptionDefaultLanguage->description1;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getRestaurantDescription2(): string
    {
        if (!empty($this->restaurantDescription->description2)) {
            return $this->restaurantDescription->description2;
        }

        if (!empty($this->restaurantDescriptionDefaultLanguage->description2)) {
            return $this->restaurantDescriptionDefaultLanguage->description2;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getRestaurantSchedule(): string
    {
        if (!empty($this->restaurantDescription->schedule)) {
            return $this->restaurantDescription->schedule;
        }

        if (!empty($this->restaurantDescriptionDefaultLanguage->schedule)) {
            return $this->restaurantDescriptionDefaultLanguage->schedule;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getRestaurantPhone(): string
    {
        if (!empty($this->restaurantDescription->phone)) {
            return $this->restaurantDescription->phone;
        }

        if (!empty($this->restaurantDescriptionDefaultLanguage->phone)) {
            return $this->restaurantDescriptionDefaultLanguage->phone;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getRestaurantAddress(): string
    {
        if (!empty($this->restaurantDescription->address)) {
            return $this->restaurantDescription->address;
        }

        if (!empty($this->restaurantDescriptionDefaultLanguage->address)) {
            return $this->restaurantDescriptionDefaultLanguage->address;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getRestaurantGmap(): string
    {
        if (!empty($this->restaurantDescription->gmap)) {
            return $this->restaurantDescription->gmap;
        }

        if (!empty($this->restaurantDescriptionDefaultLanguage->gmap)) {
            return $this->restaurantDescriptionDefaultLanguage->gmap;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getRestaurantMetaTitle(): string
    {
        if (!empty($this->restaurantDescription->meta_title)) {
            return $this->restaurantDescription->meta_title;
        }

        if (!empty($this->restaurantDescriptionDefaultLanguage->meta_title)) {
            return $this->restaurantDescriptionDefaultLanguage->meta_title;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getRestaurantMetaDescription(): string
    {
        if (!empty($this->restaurantDescription->meta_description)) {
            return $this->restaurantDescription->meta_description;
        }

        if (!empty($this->restaurantDescriptionDefaultLanguage->meta_description)) {
            return $this->restaurantDescriptionDefaultLanguage->meta_description;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getRestaurantMetaKeyword(): string
    {
        if (!empty($this->restaurantDescription->meta_keyword)) {
            return $this->restaurantDescription->meta_keyword;
        }

        if (!empty($this->restaurantDescriptionDefaultLanguage->meta_keyword)) {
            return $this->restaurantDescriptionDefaultLanguage->meta_keyword;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        $seoUrl = SeoUrl::find()->where('query = \'restaurant_id=' . $this->restaurant_id . '\'')->andWhere(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)])->one();

        if ($seoUrl) {
            return $seoUrl->keyword;
        }

        $seoUrl = SeoUrl::find()->where('query = \'restaurant_id=' . $this->restaurant_id . '\'')->andWhere(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())])->one();

        if (!empty($seoUrl)) {
            return $seoUrl->keyword;
        }

        return '';
    }

    /**
     * Returns restaurant URL.
     * @param string $seoUrl SEO URL
     * @return string full restaurant URL
     */
    public static function getUrl($seoUrl)
    {
        if ($seoUrl === '/') {
            return rtrim(Url::to(['/'], 'https'), '/');
        }

        return rtrim(Url::to(['/' . $seoUrl], 'https'), '/');
    }

    /**
     * @return mixed
     */
    public function getRestaurantName()
    {
        return $this->restaurantDescription->title;
    }

    /**
     * @return ActiveQuery
     */
    public function getRestaurantCategory(): ActiveQuery
    {
        return $this->hasOne(RestaurantCategory::class, ['restaurant_category_id' => 'restaurant_category_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['restaurant_id' => 'restaurant_id']);
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
        return (new self())->getBehavior('image')->resizeImage($filename, $width, $height, $mode, $quality);
    }

    /**
     * @param string $filename image filename
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @param int $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100). Defaults 100.
     * @return null|string image URL
     */
    public static function getBackgroundImageUrl($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_INSET, $quality = 100)
    {
        return (new self())->getBehavior('backgroundImage')->resizeImage($filename, $width, $height, $mode, $quality);
    }


    /**
     * @param string $filename image filename
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @param int $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100). Defaults 100.
     * @return null|string image URL
     */
    public static function getImageTransparentUrl($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_INSET, $quality = 100)
    {
        return (new self())->getBehavior('imageTransparent')->resizeImage($filename, $width, $height, $mode, $quality);
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
     * @return array
     */
    public static function getOnlineDeliveryList()
    {
        return [
            self::YES => 'Есть',
            self::NO => 'Нет'
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
     * @param $del
     * @return mixed|string
     */
    public static function getOnlineDeliveryName($del)
    {
        $statuses = self::getOnlineDeliveryList();
        return isset($statuses[$del]) ? $statuses[$del] : 'Неопределено';
    }

    /**
     * Returns restaurant title by specified restaurant id and language id.
     *
     * @param int $restaurantId restaurant id
     * @param int $languageId language id
     * @return false|null|string restaurant title
     */
    public static function geTitleById($restaurantId, $languageId)
    {
        return (new Query())
            ->select(['title'])
            ->from(RestaurantDescription::tableName())
            ->where(['restaurant_id' => $restaurantId, 'language_id' => $languageId])
            ->scalar();
    }

    /**
     * Returns restaurant by language id.
     *
     * @param int $id restaurant id
     * @param int $languageId language id
     * @return array restaurant
     */
    public static function getByIdAndLanguageId($id, $languageId)
    {
        return (new Query())
            ->select(['p.*', 'pd.title AS title', 'pd.content as content',
                '(CASE WHEN pd.meta_title IS NULL OR pd.meta_title = "" THEN pd.title ELSE pd.meta_title END) AS meta_title',
                'pd.meta_description as meta_description', 'pd.meta_keyword as meta_keyword'
            ])
            ->from(self::tableName() . ' AS p')
            ->leftJoin(RestaurantDescription::tableName() . ' AS pd', 'pd.restaurant_id = p.restaurant_id')
            ->where(['p.restaurant_id' => $id, 'language_id' => $languageId])
            ->groupBy('pd.restaurant_id')
            ->one();
    }

    /**
     * Returns all restaurants.
     *
     * @param int $status restaurant status to filter. Defaults 'Active'
     * @return array restaurants data
     */
    public static function getAll($status = self::STATUS_ACTIVE)
    {
        $languageId = Language::getLanguageIdByCode(Yii::$app->language);


        return (new Query())
            ->select(['p.*', 'pd.title AS restaurant_title'])
            ->from(self::tableName() . ' AS p')
            ->leftJoin(RestaurantDescription::tableName() . ' AS pd', 'pd.restaurant_id = p.restaurant_id')
            ->where(['pd.language_id' => $languageId, 'p.status' => $status])
            ->groupBy('pd.restaurant_id')
            ->orderBy('p.sort_order ASC')
            ->all();
    }

    /**
     * Returns restaurants list.
     *
     * @param int $status restaurant status to filter. Defaults 'Active'
     * @return array gapes list
     */
    public static function getList($status = self::STATUS_ACTIVE)
    {
        $result = [];

        $restaurants = self::getAll($status);

        foreach ($restaurants as $restaurant) {
            $result[$restaurant['restaurant_id']] = $restaurant['restaurant_title'];
        }

        return $result;
    }



    public static function getAvailableForAddressDelivery()
    {
        $result = [];

        $restaurants = self::find()->where(['online_delivery_orders_processing' => static::YES])->all();

        foreach ($restaurants as $restaurant) {
            $result[$restaurant['restaurant_id']] = $restaurant->getRestaurantTitleWithAddress();
        }

        return $result;
    }



    public static function getAvailableForSelfPicking()
    {
        $result = [];

        $restaurants = self::find()->where(['self_picking' => static::YES])->all();

        foreach ($restaurants as $restaurant) {
            $result[$restaurant['restaurant_id']] = $restaurant->getRestaurantTitleWithAddress();
        }

        return $result;
    }



    public static function getListWithOnlineDelivery($status = self::STATUS_ACTIVE)
    {
        $result = [];

        $languageId = Language::getLanguageIdByCode(Yii::$app->language);


        $restaurants = (new Query())
            ->select(['p.*', 'pd.title AS restaurant_title'])
            ->from(self::tableName() . ' AS p')
            ->leftJoin(RestaurantDescription::tableName() . ' AS pd', 'pd.restaurant_id = p.restaurant_id')
            ->where(['pd.language_id' => $languageId, 'p.status' => $status, 'p.online_delivery' => 1])
            ->groupBy('pd.restaurant_id')
            ->orderBy('p.sort_order ASC')
            ->all();

        foreach ($restaurants as $restaurant) {
            $result[$restaurant['restaurant_id']] = $restaurant['restaurant_title'];
        }

        return $result;

    }


    public static function getListWithCategories()
    {
        $data = self::find()->where([self::tableName() . ".status" => self::STATUS_ACTIVE, self::tableName() . ".online_delivery" => 1])
            ->joinWith(['productCategories' => static function ($q) {
                $q->joinWith('categoryDescription');
            }])->with('restaurantDescription')->asArray()->all();

        if (!empty($data)) {
            $result = [];
            foreach ($data as $key => $restaurant) {
                $result[] = [
                    'id' => $restaurant['restaurant_id'],
                    'name' => $restaurant['restaurantDescription']['title'],
                    'categories' => (static function () use ($restaurant) {
                        $data = $restaurant['productCategories'];
                        $result = [];
                        foreach ($data as $k => $category) {
                            $position = [
                                'id' => $category['category_id'],
                                'name' => $category['categoryDescription']['name']
                            ];
                            $result[] = $position;
                        }
                        return $result;
                    })()
                ];
            }
            return $result;
        }
        return [];
    }


    /**
     * @return int|string
     */
    public static function getAllCount()
    {
        return self::find()->count();
    }

    /**
     * Returns last inserted restaurant id.
     *
     * @return false|null|string
     */
    public static function getLastId()
    {
        return (new Query())
            ->select('restaurant_id')
            ->from(self::tableName())
            ->orderBy('restaurant_id DESC')
            ->limit(1)
            ->scalar();
    }
}
