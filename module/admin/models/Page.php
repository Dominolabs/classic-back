<?php

namespace app\module\admin\models;

use app\components\SitemapBehavior;
use app\module\admin\module\gallery\models\Album;
use Yii;
use app\components\ImageBehavior;
use Imagine\Image\ManipulatorInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;


/**
 * @property int $page_id
 * @property string $image
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 * @property int $top_banner_id
 * @property int $gallery_id
 * @property string $facebook
 * @property string $instagram
 * @property string $youtube
 * @property string $vk
 * @property string $footer_columns
 *
 * *
 * @property string $imageFile
 * @property $pageDescription
 * @property $pageToCategory
 */
class Page extends ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const NO = 0;
    const YES = 1;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'sort_order'], 'required'],
            [['status', 'sort_order', 'top_banner_id', 'gallery_id'], 'integer'],
            [['image', 'facebook', 'instagram', 'youtube', 'vk'], 'string', 'max' => 255],
            [['footer_columns'], 'string', 'max' => 15000],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            [['created_at', 'updated_at'], 'safe'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg',
                'maxSize' => 1024 * 1024 * 10, 'checkExtensionByMimeType' => false
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'page_id' => 'ID страницы',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'top_banner_id' => 'Баннер в шапке',
            'gallery_id' => 'Фотогалерея',
            'footer_columns' => 'Колонки в подвале (footer)',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'vk' => 'VK',
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'pageName' => 'Название',
            'categoryName' => 'Категория',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'image' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'page',
            ],
            'sitemap' => [
                'class' => SitemapBehavior::class,
                'query' => 'page_id',
                'scope' => function ($model) {
                    /** @var ActiveQuery $model */
                    $model->andWhere(['status' => Page::STATUS_ACTIVE]);
                },
                'dataClosure' => function ($model, $seoUrl) {
                    /** @var self $model */
                    /** @var string $seoUrl */
                    return [
                        'loc' => Page::getUrl($seoUrl),
                        'lastmod' => $model->updated_at,
                        'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                        'priority' => ($seoUrl === '/') ? 1 : 0.8
                    ];
                }
            ],
        ];
    }

    /**
     * Returns page URL.
     * @param string $seoUrl SEO URL
     * @return string full page URL
     */
    public static function getUrl($seoUrl)
    {
        if ($seoUrl === '/') {
            return rtrim(Url::to(['/'], 'https'), '/');
        }

        return rtrim(Url::to(['/' . $seoUrl], 'https'), '/');
    }

    /**
     * ActiveRelation to PageDescription model.
     *
     * @return ActiveQuery active query instance
     */
    public function getPageDescription()
    {
        return $this->hasOne(PageDescription::class, ['page_id' => 'page_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }


    /**
     * @return ActiveQuery
     */
    public function getBanner(): ActiveQuery
    {
        return $this->hasOne(Banner::class, ['banner_id' => 'top_banner_id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getGallery(): ActiveQuery
    {
        return $this->hasOne(Album::class, ['album_id' => 'gallery_id']);
    }

    /**
     * @return mixed
     */
    public function getPageName()
    {
        return $this->pageDescription->title;
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
     * Returns page title by specified page id and language id.
     *
     * @param int $pageId page id
     * @param int $languageId language id
     * @return false|null|string page title
     */
    public static function geTitleById($pageId, $languageId)
    {
        return (new Query())
            ->select(['title'])
            ->from(PageDescription::tableName())
            ->where(['page_id' => $pageId, 'language_id' => $languageId])
            ->scalar();
    }

    /**
     * Returns page by language id.
     *
     * @param int $id page id
     * @param int $languageId language id
     * @return array page
     */
    public static function getByIdAndLanguageId($id, $languageId)
    {
        return (new Query())
            ->select(['p.*', 'pd.*'])
            ->from(self::tableName() . ' AS p')
            ->leftJoin(PageDescription::tableName() . ' AS pd', 'pd.page_id = p.page_id')
            ->where(['p.page_id' => $id, 'language_id' => $languageId])
            ->groupBy('pd.page_id')
            ->one();
    }

    /**
     * Returns page by language id.
     *
     * @param string $slug
     * @param int $languageId language id
     * @return array page
     */
    public static function getBySlugAndLanguageId($slug, $languageId)
    {
        return (new Query())
            ->select(['p.*', 'pd.*', 'su.*'])
            ->from(self::tableName() . ' AS p')
            ->leftJoin(PageDescription::tableName() . ' AS pd', 'pd.page_id = p.page_id')
            ->leftJoin(SeoUrl::tableName() . ' AS su', 'su.query = CONCAT(\'page_id=\', p.page_id)')
            ->where(['su.keyword' => $slug, 'su.language_id' => $languageId])
            ->groupBy('pd.page_id')
            ->one();
    }

    /**
     * Returns all pages.
     *
     * @param int $status page status to filter. Defaults 'Active'
     * @return array pages data
     */
    public static function getAll($status = self::STATUS_ACTIVE)
    {
        $languageId = Language::getLanguageIdByCode(Yii::$app->language);

        return (new Query())
            ->select(['p.*', 'pd.title AS page_title'])
            ->from(self::tableName() . ' AS p')
            ->leftJoin(PageDescription::tableName() . ' AS pd', 'pd.page_id = p.page_id')
            ->where(['pd.language_id' => $languageId, 'p.status' => $status])
            ->groupBy('pd.page_id')
            ->orderBy('p.sort_order ASC')
            ->all();
    }


    /**
     * @return array
     */
    public static function getTitlesAndIdsList(): array
    {
        $languageId = Language::getLanguageIdByCode(Yii::$app->language);

        return (new Query())
            ->select(['p.page_id as id', 'pd.title AS title'])
            ->from(self::tableName() . ' AS p')
            ->leftJoin(PageDescription::tableName() . ' AS pd', 'pd.page_id = p.page_id')
            ->where(['pd.language_id' => $languageId, 'p.status' => self::STATUS_ACTIVE])
            ->groupBy('pd.page_id')
            ->orderBy('p.sort_order ASC')
            ->all();
    }



    /**
     * Returns pages list.
     *
     * @param int $status page status to filter. Defaults 'Active'
     * @return array gapes list
     */
    public static function getList($status = self::STATUS_ACTIVE)
    {
        $result = [];

        $pages = self::getAll($status);

        foreach ($pages as $page) {
            $result[$page['page_id']] = $page['page_title'];
        }

        return $result;
    }


    /**
     * Returns main menu items list.
     *
     * @param int $languageId language id
     * @return array menu items
     */
    public static function getMenuItems($languageId)
    {
        $result = [];

        $pages = (new Query())
            ->select(['p.*', 'pd.title AS page_title', 'su.keyword AS page_url'])
            ->from(self::tableName() . ' AS p')
            ->leftJoin(PageDescription::tableName() . ' AS pd', 'pd.page_id = p.page_id')
            ->leftJoin(SeoUrl::tableName() . ' AS su', "su.query = CONCAT('page_id=', pd.page_id)")
            ->where([
                'pd.language_id' => $languageId,
                'su.language_id' => $languageId,
                'p.status' => self::STATUS_ACTIVE,
            ])
            ->groupBy('pd.page_id, page_url')
            ->orderBy('p.sort_order ASC')
            ->all();

        foreach ($pages as $page) {
            $url = Url::to(['/' . $page['page_url']]);

            $result[$page['page_id']] = [
                'id' => $page['page_id'],
                'title' => $page['page_title'],
                'href' => $url,
                'active' => self::isMenuItemActive($url),
            ];
        }

        return $result;
    }

    /**
     * Checks whether menu item is active or not.
     *
     * @param string $url menu item URL
     * @return bool whether menu item is active or not
     */
    public static function isMenuItemActive($url)
    {
        return (!empty(trim($url, '/')) && (strpos(trim(Yii::$app->request->url, '/'), trim($url, '/')) !== false));
    }

    /**
     * Returns all Page models count.
     *
     * @return int|string Page models count.
     */
    public static function getAllCount()
    {
        return self::find()->count();
    }

    /**
     * Returns last inserted page id.
     *
     * @return false|null|string
     */
    public static function getLastId()
    {
        return (new Query())
            ->select('page_id')
            ->from(self::tableName())
            ->orderBy('page_id DESC')
            ->limit(1)
            ->scalar();
    }

    /**
     * Checks whether current page is homepage.
     *
     * @return bool true if current page is homepage, false otherwise
     * @throws \yii\base\InvalidConfigException
     */
    public static function isHomePage()
    {
        $actualRoute = Yii::$app->controller->getRoute();

        /** @var Controller $controller */
        /** @var string $actionId */
        list($controller, $actionId) = Yii::$app->createController('');
        $actionId = !empty($actionId) ? $actionId : $controller->defaultAction;
        $defaultRoute = $controller->getUniqueId() . '/' . $actionId;

        return $actualRoute === $defaultRoute;
    }
}
