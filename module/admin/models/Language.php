<?php
/**
 * Language model class file.
 */

namespace app\module\admin\models;

use app\components\ImageBehavior;
use app\module\api\controllers\BaseApiController;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\web\UploadedFile;
use yii\db\Query;

/**
 * This is the model class for table "{{%language}}".
 *
 * @property int $language_id
 * @property string $name
 * @property string $code
 * @property string $image
 * @property int $status
 * @property int $sort_order
 *
 * @property string $imageFile
 */
class Language extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%language}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code', 'status', 'sort_order'], 'required'],
            [['status', 'sort_order'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            [['name'], 'string', 'max' => 32],
            [['code'], 'string', 'max' => 16],
            [['image'], 'string', 'max' => 255],
            ['sort_order', 'default', 'value' => 1],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg', 'maxSize' => 1024 * 1024 * 10],
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id' => 'language_id',
            'name',
            'code',
            'image' => function() {
                return BaseApiController::BASE_SITE_URL . 'image/language/' . $this->image;
            },
            'sort_order',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'language_id' => 'ID языка',
            'name' => 'Название',
            'code' => 'Код',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'status' => 'Статус',
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
                'imageDirectory' => 'language',
            ]
        ];
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
     * Returns all languages.
     *
     * @param int|null $status language status to filter. Defaults 'Active'
     * @return array languages data
     */
    public static function getAll($status = null)
    {
        $query = (new Query())
            ->select('*')
            ->from(self::tableName());

        if ($status !== null) {
            $query = $query->where(['status' => $status]);
        }

        return $query->orderBy('sort_order ASC')->all();
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
    public static function getImageUrl($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_INSET, $quality = 100)
    {
        return (new self())->resizeImage($filename, $width, $height, $mode, $quality);
    }

    /**
     * Returns languages list.
     *
     * @param int $status language status to filter. Defaults 'Active'
     * @param bool $short true to return short names
     * @return array languages list
     */
    public static function getList($status = self::STATUS_ACTIVE, $short = false)
    {
        $key = __CLASS__ . '_languages_list';

        $result = Yii::$app->cache->get($key);

        if ($result === false) {
            $languages = self::getAll($status);

            foreach ($languages as $language) {
                $result[$language['language_id']] = $short ? mb_substr($language['name'], 0, 3) : $language['name'];
            }

            Yii::$app->cache->set($key, $result);
        }

        return $result;
    }

    /**
     * Returns codes list.
     *
     * @param int $status language status to filter. Defaults 'Active'
     * @return array language codes list
     */
    public static function getCodesList($status = self::STATUS_ACTIVE)
    {
        $result = [];

        $languages = self::getAll($status);

        foreach ($languages as $language) {
            $result[] = $language['code'];
        }

        return $result;
    }

    /**
     * Returns language name by specified language id.
     *
     * @param int $languageId language id
     * @return mixed|string language name
     */
    public static function getLanguageName($languageId)
    {
        $languages = self::getList();

        return isset($languages[$languageId]) ? $languages[$languageId] : 'Неопределено';
    }

    /**
     * Returns language id by language code.
     *
     * @param string $code language code
     * @return false|null|string language id
     */
    public static function getLanguageIdByCode($code)
    {
        $key = __CLASS__ . '_languages';

        $languages = Yii::$app->cache->get($key);

        if ($languages === false) {
            $result = [];

            $languages = self::getAll();

            foreach ($languages as $language) {
                $result[$language['code']] = $language['language_id'];
            }

            $languages = $result;

            Yii::$app->cache->set($key, $languages);
        }

        return isset($languages[$code]) ? intval($languages[$code]) : null;
    }

    /**
     * Returns language code by language id.
     *
     * @param int $languageId language id
     * @return false|null|string language code
     */
    public static function getLanguageCodeById($languageId)
    {
        return (new Query())
            ->select(['code'])
            ->from(self::tableName())
            ->where(['language_id' => $languageId])
            ->scalar();
    }

    /**
     * Returns all Language models count.
     *
     * @return int|string Language models count.
     */
    public static function getAllCount()
    {
        return self::find()->count();
    }

    /**
     * Change and save main language id in application params.
     *
     * @param int $languageId language id to set
     */
    public static function setMainLanguage($languageId)
    {
        $file = __DIR__ . '/../../../config/params.inc';

        $content = file_get_contents($file);
        $array = unserialize(base64_decode($content));

        $model = new SettingForm();
        $model->setAttributes($array);

        $model->languageId = $languageId;

        $string = base64_encode(serialize($model->getAttributes()));
        file_put_contents($file, $string);

        // Update language status
        if (($language = Language::findOne($languageId)) !== null) {
            $language->status = self::STATUS_ACTIVE;
            $language->save(false);
        }
    }
}
