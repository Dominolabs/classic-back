<?php
/**
 * Banner model class file.
 */

namespace app\module\admin\models;


use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * @property int $subscriber_id
 * @property string $name
 * @property string $email
 * @property int $created_at
 * @property int $updated_at
 */


class Subscriber extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%subscribers}}';
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'email'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'email'],  'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
            [['email'],  'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'subscriber_id' => Yii::t('subscriber', 'ID підписника'),
            'name'          => Yii::t('subscriber', 'Ім\'я'),
            'email'         => 'Email',
            'created_at'    => Yii::t('subscriber', 'Створено'),
            'updated_at'    => Yii::t('subscriber', 'Оновлено')
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

}
