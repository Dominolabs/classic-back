<?php

namespace app\models;

use Yii;
use app\module\admin\models\User;
use yii\base\Exception;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    /**
     * @var string
     */
    public $old_password;
    
    /**
     * @var string
     */
    public $new_password;
    
    /**
     * @var string
     */
    public $password_repeat;

    /**
     * @var User $user User instance
     */
    private $user;


    /**
     * @param string $id
     * @param array $config
     */
    public function __construct($id, $config = [])
    {
        $this->user = User::findOne(['user_id' => $id, 'status' => User::STATUS_ACTIVE]);

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['old_password', 'new_password', 'password_repeat'], 'required'],
            ['old_password', 'validateOldPassword'],
            ['new_password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'new_password',
                'message' => Yii::t('api', 'Паролі не співпадають.')
            ],
        ];
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function validateOldPassword($attribute, $params): void
    {
        if (!Yii::$app->security->validatePassword($this->old_password, $this->user->password_hash)) {
            $this->addError($attribute, Yii::t('api', 'Ви ввели невірний пароль.'));
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'old_password' => Yii::t('api', 'Старий пароль'),
            'new_password' => Yii::t('api', 'Новий пароль'),
            'password_repeat' => Yii::t('api', 'Повтор нового паролю'),
        ];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function changePassword(): bool
    {
        $user = $this->user;

        $user->setPassword($this->new_password);
        $user->removePasswordResetToken();
        $user->removeTemporaryPassword();
        $user->temp_password_hash = !empty($user->temp_password_hash) ? $user->temp_password_hash : '';

        return $user->save(false);
    }
}
