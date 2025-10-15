<?php

namespace app\module\admin\module\event\models;

/**
 * @property int $event_id
 * @property int $language_id
 * @property string $name
 * @property string $date
 * @property string $text
 */
class EventDescription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_event_description';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['language_id'], 'required'],
            [['name', 'date'], 'required', 'on' => 'language-is-system'],
            [['event_id', 'language_id', 'article_id'], 'integer'],
            [['name', 'date'], 'string', 'max' => 255],
            [['text'], 'string', 'max' => 10000],
            [['event_id', 'language_id'], 'unique', 'targetAttribute' => ['event_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'event_id' => 'ID',
            'language_id' => 'ID языка',
            'name' => 'Название',
            'date' => 'Дата',
            'text' => 'Описание',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->text = str_replace('/image', 'https://classic.devseonet.com/image', $this->text);

            return true;
        }

        return false;
    }

    /**
     * Removes event descriptions by event id.
     *
     * @param string $eventId event id
     */
    public static function removeByEventId($eventId)
    {
        self::deleteAll(['event_id' => $eventId]);
    }
}
