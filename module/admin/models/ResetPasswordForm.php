<?php
/**
 * ResetPasswordForm class file.
 */

namespace app\module\admin\models;

use yii\base\Model;
use yii\base\InvalidParamException;

/**
 * Class ResetPasswordForm.
 *
 * @package app\module\admin\models
 */
class ResetPasswordForm extends Model
{
    /** @var string $password */
    public $password;

    /** @var User $_user */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param string $token password reset token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Токен сброса пароля не может быть пустым.');
        }

        $this->_user = User::findByPasswordResetToken($token);

        if (!$this->_user) {
            throw new InvalidParamException('Неверный токен для сброса пароля.');
        }

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     * @throws \yii\base\Exception if failed to reset password
     */
    public function resetPassword()
    {
        $user = $this->_user;

        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }
}
