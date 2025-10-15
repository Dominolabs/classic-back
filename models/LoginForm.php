<?php

namespace app\models;

use Yii;
use app\module\admin\models\User;
use yii\base\InvalidArgumentException;
use yii\base\Model;

class LoginForm extends Model
{
    public $phone;
    public $password;
    public $token;

    private $_user;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['phone', 'password'], 'required'],
            [['token'], 'string'],
            [['password'], 'validatePassword'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'phone' => Yii::t('api', 'Номер телефону'),
            'password' => Yii::t('product', 'Пароль'),
        ];
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function validatePassword($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, Yii::t('api', 'Невірний номер телефону або пароль.'));
            } else {
                try {
                    if (!$user->validatePassword($this->password)) {
                        if ((!empty($user->temp_password_hash)) && ($user->temp_password_created_at > 0)) {
                            if (($user->temp_password_created_at < strtotime('-48 hour')) || (!$user->validateTemporaryPassword($this->password))) {
                                $this->addError($attribute, Yii::t('api', 'Невірний номер телефону або пароль.'));
                            } else {
                                Yii::$app->session->setFlash('warning', Yii::t('api',
                                    'Ви увійшли з тимчасовим паролем. Будь ласка змініть ваш пароль в особистому кабінеті.'));
                            }
                        } else {
                            $this->addError($attribute, Yii::t('api', 'Невірний номер телефону або пароль.'));
                        }
                    }
                } catch (InvalidArgumentException $exception) {
                    $this->addError($attribute, Yii::t('api', 'Невірний номер телефону або пароль.'));
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }

        return false;
    }

    /**
     * @return User|null
     */
    protected function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByPhone($this->phone);
        }

        return $this->_user;
    }
}
