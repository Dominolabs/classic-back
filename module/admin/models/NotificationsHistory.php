<?php

namespace app\module\admin\models;

use yii\behaviors\TimestampBehavior;

/**
 * @property int $notifications_history_id
 * @property string $header
 * @property string $message
 * @property int $created_at
 * @property int $updated_at
 */
class NotificationsHistory extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return 'tbl_notifications_history';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['header', 'message'], 'required'],
            [['header'], 'string', 'max' => 255],
            [['message'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'notifications_history_id' => 'ID',
            'header' =>  'Заголовок',
            'message' =>  'Сообщение',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return int|string
     */
    public static function getAllCount()
    {
        return self::find()->count();
    }
}
