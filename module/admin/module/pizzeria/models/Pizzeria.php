<?php

namespace app\module\admin\module\pizzeria\models;

use app\module\api\controllers\BaseApiController;
use Yii;
use app\components\ImageBehavior;
use Imagine\Image\ManipulatorInterface;
use app\module\admin\models\Language;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * This is the model class for table "tbl_pizzeria".
 *
 * @property int $pizzeria_id
 * @property string $image
 * @property string $phones
 * @property string $email
 * @property string $instagram
 * @property string $gmap
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 *
 * @property $pizzeriaDescription
 */
class Pizzeria extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_pizzeria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'sort_order', 'email'], 'required'],
            [['status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            [['image', 'email', 'instagram', 'gmap'], 'string', 'max' => 255],
            [['email'], 'email'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg', 'maxSize' => 1024 * 1024 * 50],
            [['phones'], 'string', 'max' => 10000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pizzeria_id' => 'ID',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'phones' => 'Номера телефонов',
            'email' => 'Email',
            'instagram' => 'Адрес страницы Instagram',
            'gmap' => 'Адрес Google Maps',
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'pizzeriaName' => 'Название',
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
                'imageDirectory' => 'pizzeria',
            ]
        ];
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPizzeriaDescription()
    {
        return $this->hasOne(PizzeriaDescription::class, ['pizzeria_id' => 'pizzeria_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * @return mixed
     */
    public function getPizzeriaName()
    {
        return $this->pizzeriaDescription->name;
    }

    /**
     * @return array statuses
     */
    public static function getStatusesList()
    {
        return [
            self::STATUS_ACTIVE => 'Включено',
            self::STATUS_NOT_ACTIVE => 'Отключено'
        ];
    }

    /**
     * @param integer $status
     * @return mixed|string
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
     * @param string $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100). Defaults 100.
     * @return null|string image URL
     */
    public static function getImageUrl($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND, $quality = 100)
    {
        return (new self())->resizeImage($filename, $width, $height, $mode, $quality);
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function getOriginalImagePath($filename)
    {
        return (new self())->getImagePath() . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * @param int $status
     * @param string $order
     * @param int $limit
     * @return array
     */
    public static function getAll($status = self::STATUS_ACTIVE, $order = 'p.sort_order ASC', $limit = null)
    {
        $query = (new Query())
            ->select('p.*, (CASE WHEN pd.name != "" THEN pd.name ELSE pd2.name END) as name,
                (CASE WHEN pd.address != "" THEN pd.address ELSE pd2.address END) as address, 
                (CASE WHEN pd.schedule != "" THEN pd.schedule ELSE pd2.schedule END) as schedule')
            ->from(self::tableName() . ' AS p')
            ->leftJoin(PizzeriaDescription::tableName() . ' AS pd', 'p.pizzeria_id = pd.pizzeria_id AND pd.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(PizzeriaDescription::tableName() . ' AS pd2', 'p.pizzeria_id = pd2.pizzeria_id AND pd2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where(['p.status' => $status])
            ->groupBy('pd.pizzeria_id')
            ->orderBy($order)
            ->limit($limit);

        return $query->all();
    }


    public static function getAllForSelfPicking ()
    {
        return static::find()
            ->select([static::tableName() . '.pizzeria_id'])
            ->where([static::tableName() . '.status' => static::STATUS_ACTIVE])
            ->joinWith(['pizzeriaDescription'])
            ->all();
    }

    /**
     * @return int|string
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
        $result = [];
        $pizzerias = self::getAll($status);

        foreach ($pizzerias as $pizzeria) {
            $result[$pizzeria['pizzeria_id']] = $pizzeria['name'];
        }

        return $result;
    }
}
