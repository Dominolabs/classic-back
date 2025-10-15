<?php

namespace app\module\admin\models;

use yii\behaviors\TimestampBehavior;

/**
 * @property int user_notifications_history_id
 * @property int $user_id
 * @property string $header
 * @property string $message
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserNotificationsHistory extends \yii\db\ActiveRecord
{
    const STATUS_NOT_READ = 0;
    const STATUS_READ = 1;

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'tbl_user_notifications_history';
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
            'user_notifications_history_id' => 'ID',
            'user_id' =>  'Пользователь',
            'header' =>  'Заголовок',
            'message' =>  'Сообщение',
            'status' =>  'Статус',
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

    /**
     * @return array|false
     */
    public function fields()
    {
        return [
            'id' => 'user_notifications_history_id',
            'title' => 'header',
            'text' => 'message',
            'date' => 'created_at',
            'read' => 'status',
        ];
    }
}
