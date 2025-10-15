<?php

namespace app\module\admin\module\gallery\models;

use app\module\api\controllers\BaseApiController;
use Imagine\Image\ManipulatorInterface;
use Yii;
use app\module\admin\models\SeoUrl;
use app\components\ImageBehavior;
use app\module\admin\models\Language;
use yii\behaviors\TimestampBehavior;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * @property int $album_id
 * @property string $image
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 *
 * @property AlbumDescription $albumDescription
 * @property AlbumDescription $albumDescriptionDefaultLanguage
 * @property $albumCategoryDescription
 * @property $albumImages
 * @property $albumName
 */
class Album extends ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @var UploadedFile
     */
    public $imageFile;
    /**
     * @var string
     */
    public $images;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_album';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'sort_order'], 'required'],
            [['status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['image'], 'string', 'max' => 255],
            [['images'], 'string'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg', 'maxSize' => 1024 * 1024 * 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'album_id' => 'ID',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'albumName' => 'Название',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'image' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'album',
            ]
        ];
    }

    public function fields()
    {
        return [
            'id' => 'album_id',
            'name' => 'albumName',
            'image' => static function($album) {
                if (!empty($album->image) && file_exists($album->getImagePath() . DIRECTORY_SEPARATOR . $album->image)) {
                    return BaseApiController::BASE_SITE_URL . 'image/album/' . $album->image;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'image_preview' => static function($album) {
                if (!empty($album->image) && file_exists($album->getImagePath() . DIRECTORY_SEPARATOR . $album->image)) {
                    return BaseApiController::BASE_SITE_URL . trim($album->resizeImage($album->image, 300, 300), '/');
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'sort_order',
            'created_at',
            'updated_at',
            'images' => 'albumImages',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAlbumImages(): ActiveQuery
    {
        return $this->hasMany(AlbumImage::class, ['album_id' => 'album_id'])->orderBy('sort_order ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getAlbumDescription(): ActiveQuery
    {
        return $this->hasOne(AlbumDescription::class, ['album_id' => 'album_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * @return ActiveQuery active query instance
     */
    public function getAlbumDescriptionDefaultLanguage(): ActiveQuery
    {
        return $this->hasOne(AlbumDescription::class, ['album_id' => 'album_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())]);
    }

    /**
     * Returns album name.
     *
     * @return mixed album name
     */
    public function getAlbumName()
    {
        if (!empty($this->albumDescription->name)) {
            return $this->albumDescription->name;
        }

        if (!empty($this->albumDescriptionDefaultLanguage->name)) {
            return $this->albumDescriptionDefaultLanguage->name;
        }

        return '';
    }

    /**
     * Sets images.
     * @param ArrayDataProvider $albumImagesDataProvider album images data provider
     */
    public function setImages($albumImagesDataProvider)
    {
        $result = [];
        /** @var ArrayDataProvider $albumImagesDataProvider */
        /** @var AlbumImage $albumImage */
        foreach ($albumImagesDataProvider->getModels() as $albumImage) {
            $result[] = $albumImage->image;
        }
        $this->images = Json::encode($result);
    }

    /**
     * @param int $status
     * @return array
     */
    public static function getList($status = self::STATUS_ACTIVE)
    {
        $result = [];

        $albums = self::getAll($status);

        foreach ($albums as $album) {
            $result[$album['album_id']] = $album['name'];
        }

        return $result;
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
     * Returns all albums.
     *
     * @param int $status album status to filter. Defaults 'Active'
     * @param string $order order condition. Defaults 'e.sort_order ASC'
     * @param int $limit items limit. Defaults null
     * @return array albums data
     */
    public static function getAll($status = self::STATUS_ACTIVE, $order = 'a.sort_order ASC', $limit = null)
    {
        $query = (new Query())
            ->select('a.*, (CASE WHEN ad.name != "" THEN ad.name ELSE ad2.name END) as name')
            ->from(self::tableName() . ' AS a')
            ->leftJoin(AlbumDescription::tableName() . ' AS ad', 'a.album_id = ad.album_id AND ad.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(AlbumDescription::tableName() . ' AS ad2', 'a.album_id = ad2.album_id AND ad2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where(['a.status' => $status])
            ->groupBy('ad.album_id')
            ->orderBy($order)
            ->limit($limit);

        return $query->all();
    }

    /**
     * Returns album data.
     *
     * @param int $albumId album id
     * @return array|bool album data
     */
    public static function getAlbum($albumId)
    {
        return (new Query())
            ->select('*, (CASE WHEN ad.name != "" THEN ad.name ELSE ad2.name END) as name')
            ->distinct()
            ->from(self::tableName() . ' AS a')
            ->leftJoin(AlbumDescription::tableName() . ' AS ad', 'a.album_id = ad.album_id AND ad.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(AlbumDescription::tableName() . ' AS ad2', 'a.album_id = ad2.album_id AND ad2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where('a.album_id = ' . $albumId . ' AND ad.language_id = ' . Language::getLanguageIdByCode(Yii::$app->language) . ' AND a.status = ' . self::STATUS_ACTIVE)
            ->one();
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
     * Returns album URL.
     *
     * @param int $albumId album id
     * @param string $pageUrl related page URL
     * @return false|null|string assembly URL
     */
    public static function getUrl($albumId, $pageUrl)
    {
        return Url::to([$pageUrl . '/gallery/' . self::getSeoUrl($albumId)]);
    }

    /**
     * Returns album SEO URL.
     *
     * @param int $albumId album id
     * @return false|null|string album SEO URL
     */
    public static function getSeoUrl($albumId)
    {
        return (new Query())
            ->select('keyword')
            ->from(SeoUrl::tableName())
            ->where([
                'query' => 'album_id=' . $albumId,
                'language_id' => Language::getLanguageIdByCode(Yii::$app->language),
            ])
            ->scalar();
    }

    /**
     * @param int $status
     * @return array
     */
    public static function getAlbumList($status = self::STATUS_ACTIVE)
    {
        $models = self::find()->with('albumDescription')->where(['status' => $status])->all();

        $result[0] = null;
        foreach ($models as $model)
        {
            /** @var self $model */
            $result[$model->album_id] = $model->albumName;
        }
        return $result;
    }
}
