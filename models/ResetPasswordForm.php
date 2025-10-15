<?php
/**
 * PasswordResetRequestForm model class file.
 */

namespace app\models;

use Yii;
use app\module\admin\models\User;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class ResetPasswordForm.
 *
 * @package app\models
 */
class ResetPasswordForm extends Model
{
    /**
     * @var string $password_reset_token
     */
    public $password_reset_token;

    /**
     * @var string $password
     */
    public $password;

    /**
     * @var string $password_repeat
     */
    public $password_repeat;

    /**
     * @var string $error error message
     */
    public $error;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password_reset_token', 'password', 'password_repeat'], 'required'],
            [['password'], 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password',
                'message' => Yii::t('api', 'Паролі не співпадають.')
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'password_reset_token' => Yii::t('api', 'Токен скидання паролю'),
            'password' => Yii::t('api', 'Пароль'),
            'password_repeat' => Yii::t('api', 'Повтор нового паролю'),
        ];
    }

    /**
     * @param User $user
     * @return bool whether password was reset
     * @throws Exception
     */
    public function resetPassword($user): bool
    {
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        $user->removeTemporaryPassword();

        return $user->save(false);
    }
}