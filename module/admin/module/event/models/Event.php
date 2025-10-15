<?php

namespace app\module\admin\module\event\models;

use app\module\admin\module\gallery\models\Album;
use app\module\api\controllers\BaseApiController;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\jobs\ImageCopiesJob;
use Yii;
use app\components\ImageBehavior;
use Imagine\Image\ManipulatorInterface;
use app\module\admin\models\Language;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\web\UploadedFile;
use cornernote\linkall\LinkAllBehavior;



/**
 * @property int $event_id
 * @property string $image
 * @property int $event_category_id
 * @property int $gallery_id
 * @property int $status
 * @property int $sort_order
 * @property string $title
 * @property string $description
 * @property string $slug
 * @property int $created_at
 * @property int $updated_at
 * @property string _video_urls
 *
 * @property $eventDescription
 * @property array  $videoUrls
 * @property $eventCategoryDescription
 */
class Event extends \yii\db\ActiveRecord
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
        return 'tbl_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['event_category_id', 'status', 'sort_order'], 'required'],
            [['event_category_id', 'status', 'sort_order', 'created_at', 'updated_at', 'gallery_id'], 'integer'],
            [['image', 'title', 'description', 'slug'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg', 'maxSize' => 1024 * 1024 * 10],
            ['videoUrls', 'nullableArrayOrString'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'event_id'          => 'ID',
            'image'             => 'Изображение',
            'imageFile'         => 'Изображение',
            'event_category_id' => 'Категория',
            'status'            => 'Статус',
            'sort_order'        => 'Порядок сортировки',
            'title'             => 'Title',
            'description'       => 'Description',
            'slug'              => 'Slug',
            'created_at'        => 'Создано',
            'updated_at'        => 'Обновлено',
            'eventName'         => 'Название',
            'eventDate'         => 'Дата',
            'videoUrls'         => 'Відео з Youtube',
            'gallery_id'        => 'Галерея',
        ];
    }


    public function fields()
    {
        return [
            'id' => 'event_id',
            'image' => static function($model){
                return $model->getImageForField();
            },
            'image_l' => static function ($model) {
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_l.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/event/' . $model->image . '_l.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    return Helper::asset('image/event/' . $model->image);
                }

                return Helper::asset('image/placeholder.png');
            },
            'image_m' => static function ($model) {
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_m.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/event/' . $model->image . '_m.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    return Helper::asset('image/event/' . $model->image);
                }

                return Helper::asset('image/placeholder.png');
            },
            'image_s' => static function ($model) {
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_s.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/event/' . $model->image . '_s.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    return Helper::asset('image/event/' . $model->image);
                }

                return Helper::asset('image/placeholder.png');
            },
            'image_xs' => static function ($model) {
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_xs.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/event/' . $model->image . '_xs.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    Yii::$app->queue->push(new ImageCopiesJob([
                        'file' => $model->getImagePath() . DIRECTORY_SEPARATOR . $model->image
                    ]));
                    return Helper::asset('image/event/' . $model->image);
                }
                $model->image = '';
                $model->save(false);

                return Helper::asset('image/placeholder.png');
            },
            'image_preview' => static function($model){
                return $model->getImageForField(false);
            },
            'title' => 'eventName',
            'text'  => 'eventText',
            'tags'  => 'eventTags',
            'date'  => 'eventUnixDate',
            'meta_title'  => static function($model){
                return $model->title;
            },
            'meta_description'  => static function($model){
                return $model->description;
            },
            'slug'  => static function($model){
                return $model->slug;
            },
            'videoUrls',
            'gallery',
        ];
    }


    /**
     * @param bool $original
     * @return string
     */
    protected function getImageForField($original = true): string
    {
        if (!empty($this->image) && file_exists($this->getImagePath() . DIRECTORY_SEPARATOR . $this->image)) {
            return ($original)
                ? BaseApiController::BASE_SITE_URL . 'image/event/' . $this->image
                : BaseApiController::BASE_SITE_URL . trim($this->resizeImage($this->image, 500, 500), '/');
        }
        return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
    }


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            LinkAllBehavior::class,
            'image' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'event',
            ]
        ];
    }

    /**
     * ActiveRelation to EventDescription model.
     *
     * @return ActiveQuery active query instance
     */
    public function getEventDescription()
    {
        return $this->hasOne(EventDescription::class, ['event_id' => 'event_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * ActiveRelation to EventCategoryDescription model.
     *
     * @return ActiveQuery active query instance
     */
    public function getEventCategoryDescription(): ActiveQuery
    {
        return $this->hasOne(EventCategoryDescription::class, ['event_category_id' => 'event_category_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }


    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getTags(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['tag_id' => 'tag_id'])
            ->joinWith('tagDescription')
            ->viaTable('tbl_event_tag', ['event_id' => 'event_id']);
    }


    public function getEventTags()
    {
        $tags = $this->tags;

        return $tags;
    }


    /**
     * @return mixed
     */
    public function getEventName()
    {
        if(isset($this->eventDescription->name)) {
            return $this->eventDescription->name;
        }
    }

    /**
     * @return mixed
     */
    public function getEventDate()
    {
        if(isset($this->eventDescription->date)) {
            return $this->eventDescription->date;
        }
    }


    /**
     * @return int|null
     */
    public function getEventUnixDate(): ?int
    {
        $string_date = $this->getEventDate();
        if(isset($string_date) && !empty($string_date)){
            $date = \DateTime::createFromFormat('d.m.Y', $string_date);
            return $date->getTimestamp();
        }
        return null;
    }


    /**
     * @return mixed
     */
    public function getEventText()
    {
        if(isset($this->eventDescription->text)) {
            return $this->eventDescription->text;
        }
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

    /**
     * Returns all events.
     *
     * @param int $status event status to filter. Defaults 'Active'
     * @param string $order order condition. Defaults 'e.sort_order ASC'
     * @param int $limit items limit. Defaults null
     * @return array events data
     */
    public static function getAll($status = self::STATUS_ACTIVE, $order = 'e.sort_order ASC', $limit = null)
    {
        $query = (new Query())
            ->select('e.*, (CASE WHEN ed.name != "" THEN ed.name ELSE ed2.name END) as name,
                (CASE WHEN ed.date != "" THEN ed.date ELSE ed2.date END) as date,
                (CASE WHEN ed.text != "" THEN ed.text ELSE ed2.text END) as text')
            ->from(self::tableName() . ' AS e')
            ->leftJoin(EventDescription::tableName() . ' AS ed', 'e.event_id = ed.event_id AND ed.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(EventDescription::tableName() . ' AS ed2', 'e.event_id = ed2.event_id AND ed2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where(['e.status' => $status])
            ->groupBy('ed.event_id')
            ->orderBy($order)
            ->limit($limit);

        return $query->all();
    }

    /**
     * Returns events by event category id.
     *
     * @param int $eventCategoryId event category id
     * @param int $status event status to filter. Defaults 'Active'
     * @param string $order order condition. Defaults 'e.sort_order ASC'
     * @param int $limit items limit. Defaults null
     * @return array events data
     */
    public static function getByEventCategoryId($eventCategoryId, $status = self::STATUS_ACTIVE, $order = 'e.sort_order ASC', $limit = null)
    {
        $query = (new Query())
            ->select('e.*, (CASE WHEN ed.name != "" THEN ed.name ELSE ed2.name END) as name,
                (CASE WHEN ed.date != "" THEN ed.date ELSE ed2.date END) as date,
                (CASE WHEN ed.text != "" THEN ed.text ELSE ed2.text END) as text')
            ->from(self::tableName() . ' AS e')
            ->leftJoin(EventDescription::tableName() . ' AS ed', 'e.event_id = ed.event_id AND ed.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(EventDescription::tableName() . ' AS ed2', 'e.event_id = ed2.event_id AND ed2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where([
                'e.event_category_id' => $eventCategoryId,
                'e.status' => $status
            ])
            ->groupBy('ed.event_id')
            ->orderBy($order)
            ->limit($limit);

        return $query->all();
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
     * @param $attribute
     * @param $params
     */
    public function nullableArrayOrString($attribute, $params)
    {
        if (isset($this->$attribute) && !is_null($this->$attribute) && !is_array($this->$attribute) && !is_string($this->$attribute))
            $this->addError($attribute, 'This attribute should be an array.');
    }

    /**
     * @param bool $single
     * @return array|mixed
     */
    public function getVideoUrls($single = true)
    {
        if (empty($this->_video_urls)) return $single ? '' : [];
        $arr = (json_decode($this->_video_urls));
        if (is_array($arr)) return $single ? $arr[array_key_first($arr)] : $arr;
        return $single ? '' : [];
    }

    /**
     * @param $value
     * @return bool
     */
    public function setVideoUrls($value)
    {
        if (is_string($value)) {
            $this->_video_urls = json_encode([$value]);
            return true;
        }

        if (empty($value)) {
            $this->_video_urls = null;
            return true;
        }

        if (is_array($value)) {
            $this->_video_urls = json_encode($value);
            return true;
        }
        return false;
    }

    /**
     * @return ActiveQuery
     */
    public function getGallery()
    {
        return $this->hasOne(Album::class, ['album_id' => 'gallery_id']);
    }
}
