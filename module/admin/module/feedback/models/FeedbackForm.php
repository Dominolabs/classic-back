<?php

namespace app\module\admin\module\feedback\models;

use Yii;
use yii\base\Model;


/**
 * @property string $text
 * @property string $name
 * @property string $email
 * @property string $phone
 */


class FeedbackForm extends Model
{
    public $text;
    public $name;
    public $email;
    public $phone;



    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'text', 'phone', 'email'], 'required'],
            ['email', 'email'],
            [['phone'], 'string', 'min' => 12,'max' => 12],
            [['text'],  'string', 'max' => 3000],
            [['name', 'phone', 'email'],  'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name' =>  Yii::t('reviews', 'Ім\'я'),
            'phone' => Yii::t('reviews', 'Телефон'),
            'email' => Yii::t('reviews', 'Email'),
            'text' => Yii::t('reviews', 'Відгук')
        ];
    }
}
