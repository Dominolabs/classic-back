<?php

namespace app\module\admin\module\event\models;

/**
 * @property int $tag_id
 * @property int $language_id
 * @property string $name
 */

class TagDescription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_tag_description';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tag_id', 'language_id'], 'required'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['tag_id', 'language_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['tag_id', 'language_id'], 'unique', 'targetAttribute' => ['tag_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => 'ID',
            'language_id' => 'ID языка',
            'name' => 'Название',
        ];
    }

    /**
     * Removes tag descriptions by tag id.
     *
     * @param string $tagId tag id
     */
    public static function removeByEventId($tagId)
    {
        self::deleteAll(['tag_id' => $tagId]);
    }
}
