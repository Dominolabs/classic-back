<?php
/**
 * SourceMessage model class file.
 */

namespace app\module\admin\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_source_message".
 *
 * @property int $source_message_id
 * @property string $category
 * @property string $message
 */
class SourceMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_source_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category', 'message'], 'required'],
            [['message'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'source_message_id' => 'ID сообщения',
            'category' => 'Категория',
            'message' => 'Сообщение',
        ];
    }

    /**
     * Returns last inserted
     * @return false|null|string
     */
    public static function getLastId()
    {
        return (new Query())
            ->select('source_message_id')
            ->from(self::tableName())
            ->orderBy('source_message_id DESC')
            ->limit(1)
            ->scalar();
    }
}
