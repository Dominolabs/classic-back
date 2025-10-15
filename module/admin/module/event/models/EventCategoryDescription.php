<?php

namespace app\module\admin\module\event\models;

/**
 * @property int $event_category_id
 * @property int $language_id
 * @property string $name
 */
class EventCategoryDescription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_event_category_description';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['event_category_id', 'language_id'], 'required'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['event_category_id', 'language_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['event_category_id', 'language_id'], 'unique', 'targetAttribute' => ['event_category_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'event_category_id' => 'ID категории события',
            'language_id' => 'ID языка',
            'name' => 'Название',
        ];
    }

    /**
     * Removes event categories by event category id.
     *
     * @param string $eventCategoryId event category id
     */
    public static function removeByEventCategoryId($eventCategoryId)
    {
        self::deleteAll(['event_category_id' => $eventCategoryId]);
    }
}
