<?php
/**
 * BannerImage model class file.
 */

namespace app\module\admin\models;

use app\components\ImageBehavior;
use app\module\api\controllers\BaseApiController;
use yii\db\Query;
use yii\web\UploadedFile;
use Imagine\Image\ManipulatorInterface;

/**
 * This is the model class for table "{{%banner_image}}".
 *
 * @property int $banner_image_id
 * @property int $banner_id
 * @property int $language_id
 * @property string $title
 * @property string $link
 * @property string $image
 * @property int $sort_order
 *
 * @property string $imageFile
 * @property bool $removeImageFile
 */
class BannerImage extends \yii\db\ActiveRecord
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%banner_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['banner_id', 'language_id', 'sort_order'], 'integer'],
            [['title', 'link', 'image'], 'string', 'max' => 255],
            ['sort_order', 'default', 'value' => 1],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg', 'maxSize' => 1024 * 1024 * 10],
            [['removeImageFile'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'banner_image_id' => 'ID изображения',
            'banner_id' => 'ID баннера',
            'language_id' => 'Язык',
            'title' => 'Название',
            'link' => 'Ссылка',
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
                'imageDirectory' => 'banner',
            ]
        ];
    }

    public function fields()
    {
        return [
            'id' => 'banner_image_id',
            'banner_id',
            'language_id',
            'title',
            'link',
            'image' => static function($bannerImage) {
                if (!empty($bannerImage->image) && file_exists($bannerImage->getImagePath() . DIRECTORY_SEPARATOR . $bannerImage->image)) {
                    return BaseApiController::BASE_SITE_URL . 'image/banner/' . $bannerImage->image;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'image_preview' => static function($bannerImage) {
                if (!empty($bannerImage->image) && file_exists($bannerImage->getImagePath() . DIRECTORY_SEPARATOR . $bannerImage->image)) {
                    return BaseApiController::BASE_SITE_URL . trim($bannerImage->resizeImage($bannerImage->image, 300, 300), '/');
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'sort_order',
        ];
    }

    /**
     * Returns all banner images by banner id and language id.
     *
     * @param int $bannerId
     * @param int $languageId
     * @return static[] an array of banner images ActiveRecord instances, or an empty array if nothing matches
     */
    public static function getAllByBannerIdAndLanguageId($bannerId, $languageId)
    {
        return self::findAll(['banner_id' => $bannerId, 'language_id' => $languageId]);
    }

    /**
     * Returns all banner images by banner id and language id.
     *
     * @param int $bannerId
     * @param int $languageId
     * @return array an array of banner images data, or an empty array if nothing matches
     */
    public static function getAllByBannerIdAndLanguageIdAR($bannerId, $languageId)
    {
        return (new Query())
            ->select(['banner_image_id', 'banner_id', 'language_id', 'title', 'link', 'image', 'sort_order'])
            ->from(self::tableName())
            ->where(['banner_id' => $bannerId, 'language_id' => $languageId])
            ->orderBy('sort_order')
            ->all();
    }

    /**
     * Removes banner images by banner id.
     *
     * @param string $bannerId banner id
     * @param bool $removeImages whether need remove images
     * @throws \Exception|\Throwable in case delete failed
     * @throws \yii\db\StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     */
    public static function removeByBannerId($bannerId, $removeImages = false)
    {
        /** @var BannerImage | ImageBehavior $banner */
        foreach (BannerImage::find()->where(['banner_id' => $bannerId])->all() as $banner) {
            if ($removeImages) {
                $banner->removeImage($banner->image);
            }
            $banner->delete();
        }
    }

    /**
     * Returns image URL.
     *
     * @param string $filename image filename
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @param string $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100). Defaults 100.
     * @return null|string image URL
     */
    public static function getImageUrl($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND, $quality = 100)
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
}
