<?php
/**
 * Banner model class file.
 */

namespace app\module\admin\models;


use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * @property string $name
 * @property string $email
 */


class SubscriberForm extends Model
{
    public $name;
    public $email;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name', 'email'], 'required'],
            [['name', 'email'],  'string', 'max' => 255],
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
