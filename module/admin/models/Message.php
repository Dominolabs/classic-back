<?php
/**
 * Message model class file.
 */

namespace app\module\admin\models;

use Yii;

/**
 * This is the model class for table "tbl_message".
 *
 * @property int $source_message_id
 * @property int $language_id
 * @property string $translation
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source_message_id', 'language_id', 'translation'], 'required'],
            [['source_message_id', 'language_id'], 'integer'],
            [['translation'], 'string'],
            [['source_message_id', 'language_id'], 'unique', 'targetAttribute' => ['source_message_id', 'language_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'source_message_id' => 'ID перевода',
            'language_id' => 'ID языка',
            'translation' => 'Перевод',
        ];
    }

    /**
     * Removes messages by source message id.
     *
     * @param string $sourceMessageId source message id
     */
    public static function removeBySourceMessageId($sourceMessageId)
    {
        self::deleteAll(['source_message_id' => $sourceMessageId]);
    }
}
