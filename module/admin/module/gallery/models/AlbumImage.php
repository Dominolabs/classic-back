<?php

namespace app\module\admin\module\gallery\models;

use app\components\ImageBehavior;
use app\module\api\controllers\BaseApiController;
use Exception;
use Imagine\Image\ManipulatorInterface;
use Throwable;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * @property int $album_image_id
 * @property int $album_id
 * @property string $image
 * @property int $sort_order
 */
class AlbumImage extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $imageFile;
    /**
     * @var bool whether need remove image file after remove model
     */
    public $removeImageFile = true;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_album_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['album_id', 'sort_order'], 'integer'],
            [['image'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg', 'maxSize' => 1024 * 1024 * 10],
            [['removeImageFile'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'album_image_id' => 'ID изображения альбома',
            'album_id' => 'ID альбома',
            'image' => 'Изображение',
            'sort_order' => 'Порядок сортировки',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'image' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'album',
            ]
        ];
    }

    public function fields()
    {
        return [
            'id' => 'album_image_id',
            'album_id',
            'image' => static function($albumImage) {
                if (!empty($albumImage->image) && file_exists($albumImage->getImagePath() . DIRECTORY_SEPARATOR . $albumImage->image)) {
                    return BaseApiController::BASE_SITE_URL . 'image/album/' . $albumImage->image;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'image_preview' => static function($albumImage) {
                if (!empty($albumImage->image) && file_exists($albumImage->getImagePath() . DIRECTORY_SEPARATOR . $albumImage->image)) {
                    return BaseApiController::BASE_SITE_URL . trim($albumImage->resizeImage($albumImage->image, 300, 300), '/');
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'sort_order',
        ];
    }

    /**
     * Moves uploaded file from temporary directory to destination directory.
     */
    public function moveUploadedFile()
    {
        $oldPath = Yii::getAlias('@app/web/image/temp') . DIRECTORY_SEPARATOR . $this->image;
        $newPath = $this->getImagePath() . DIRECTORY_SEPARATOR . $this->image;

        if (!file_exists($newPath) && file_exists($oldPath) && copy($oldPath, $newPath)) {
            copy($oldPath, $this->getImageCachePath() . DIRECTORY_SEPARATOR . $this->image);
            @unlink($oldPath);
        }
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
     * Returns all album images by album id.
     *
     * @param int $albumId album id
     * @return static[] an array of banner images ActiveRecord instances, or an empty array if nothing matches
     */
    public static function getAllByAlbumId($albumId)
    {
        return self::findAll(['album_id' => $albumId]);
    }

    /**
     * Removes album images by album id.
     *
     * @param string $albumId album id
     * @param bool $removeImages whether need remove images
     * @throws Exception|Throwable in case delete failed
     * @throws StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     */
    public static function removeByAlbumId($albumId, $removeImages = false)
    {
        /** @var AlbumImage | ImageBehavior $album */
        foreach (AlbumImage::find()->where(['album_id' => $albumId])->all() as $album) {
            if ($removeImages) {
                $album->removeImage($album->image);
            }
            $album->delete();
        }
    }

    /**
     * Returns album images data provider.
     * @param int $albumId album id
     * @return ArrayDataProvider album images data provider
     */
    public static function getDataProvider($albumId)
    {
        return new ArrayDataProvider([
            'key' => 'album_image_id',
            'modelClass' => self::class,
            'allModels' => self::getAllByAlbumId($albumId),
            'pagination' => false
        ]);
    }

    /**
     * Returns album images initial preview.
     * @param ArrayDataProvider $albumImagesDataProvider album images data provider
     * @return array album images initial preview config
     */
    public static function getInitialPreview($albumImagesDataProvider)
    {
        $result = [];
        /** @var AlbumImage $albumImageModel */
        foreach ($albumImagesDataProvider->getModels() as $albumImageModel) {
            $result[] = self::getImageUrl($albumImageModel->image, 600, 600);
        }
        return $result;
    }

    /**
     * Returns album images initial preview.
     * @param ArrayDataProvider $albumImagesDataProvider album images data provider
     * @return array album images initial preview config
     */
    public static function getInitialPreviewConfig($albumImagesDataProvider)
    {
        $result = [];
        /** @var AlbumImage $albumImageModel */
        foreach ($albumImagesDataProvider->getModels() as $albumImageModel) {
            $result[] = [
                'caption' => $albumImageModel->image,
                'url' => Url::to(['album/delete-image']),
                'key' => $albumImageModel->image
            ];
        }
        return $result;
    }
}
