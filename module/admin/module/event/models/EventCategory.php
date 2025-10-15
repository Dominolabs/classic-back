<?php

namespace app\module\admin\module\event\models;

use Yii;
use app\module\admin\models\Language;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\db\ActiveRecord;

/**
 * @property int $event_category_id
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 *
 * @property $eventCategoryDescription
 */
class EventCategory extends ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_event_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'sort_order'], 'required'],
            [['status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'event_category_id' => 'ID категории события',
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'eventCategoryName' => 'Название'
        ];
    }


    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id' =>'event_category_id',
            'name' => 'eventCategoryName',
            'tags'
        ];
    }



    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * ActiveRelation to EventCategoryDescription model.
     *
     * @return ActiveQuery active query instance
     */
    public function getEventCategoryDescription()
    {
        return $this->hasOne(EventCategoryDescription::class, ['event_category_id' => 'event_category_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * Returns event category name.
     *
     * @return mixed event category name
     */
    public function getEventCategoryName()
    {
        return $this->eventCategoryDescription->name;
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
     * Returns event categories list.
     *
     * @param int $status event category status to filter. Defaults 'Active'
     * @return array event categories list
     */
    public static function getList($status = self::STATUS_ACTIVE)
    {
        $result = [];

        $eventCategories = self::getAll($status);

        foreach ($eventCategories as $eventCategory) {
            $result[$eventCategory['event_category_id']] = $eventCategory['name'];
        }

        return $result;
    }

    /**
     * Returns all event categories.
     *
     * @param int $status event category status to filter. Defaults 'Active'
     * @return array event categories data
     */
    public static function getAll($status = self::STATUS_ACTIVE)
    {
        return (new Query())
            ->select('ac.*, acd.name AS name')
            ->from(self::tableName() . ' AS ac')
            ->leftJoin(EventCategoryDescription::tableName() . ' AS acd', 'acd.event_category_id = ac.event_category_id')
            ->where(['acd.language_id' => Language::getLanguageIdByCode(Yii::$app->language), 'ac.status' => $status])
            ->groupBy('acd.event_category_id')
            ->orderBy('ac.sort_order ASC')
            ->all();
    }


    public function getName()
    {

    }

    /**
     * @return ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::class, ['event_category_id' => 'event_category_id']);
    }

    /**
     * @return array|ActiveRecord[]
     */
    public function getTags()
    {
        return Tag::find()->joinWith('events', false)
        ->where([ Event::tableName() . '.event_category_id' => $this->event_category_id])
            ->with('tagDescription')
            ->all();
    }
}
