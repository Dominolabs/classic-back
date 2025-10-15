<?php

namespace app\models;

use Yii;
use yii\base\Model;

class WebcamForm extends Model
{
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => Yii::t('webcam', 'Код'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (strtolower($this->password) !== strtolower(Yii::$app->params['webCamPassword'])) {
            $this->addError($attribute, Yii::t('webcam', 'Невірний пароль.'));
        }
    }
}
