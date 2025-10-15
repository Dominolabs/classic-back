<?php

namespace app\models;

use Yii;
use app\module\admin\models\User;
use yii\base\Exception;
use yii\base\Model;

class PasswordResetRequestForm extends Model
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $type = 'password';


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist', 'targetClass' => User::class, 'filter' => ['status' => User::STATUS_ACTIVE], 'message' => Yii::t('api', 'Користувача з такою адресою електронної пошти не знайдено.'),],
            ['type', 'trim']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('api', 'E-mail'),
        ];
    }

    /**
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public function sendEmail($type): bool
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();

            if (!$user->save(false)) {
                return false;
            }
        }

        $siteName = Yii::$app->params['siteName'] ?? Yii::$app->name;

        $user->refresh();

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => '@app/mail/frontend/passwordResetToken-html', 'text' => '@app/mail/frontend/passwordResetToken-text'],
                [
                    'user' => $user,
                ]
            )
            ->setFrom(['superrobot@ukr.net' => $siteName . ' ' . Yii::t('api', 'робот')])
            ->setTo($this->email)
            ->setSubject(Yii::t('api', 'Скидання паролю для') . ' ' . $siteName)
            ->send();
    }
}
