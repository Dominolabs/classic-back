<?php

namespace app\models;

use Yii;
use app\components\ImageBehavior;
use app\module\admin\models\User;
use yii\base\Exception;
use yii\base\Model;

class SignupForm extends Model
{
    public $phone;
    public $email;
    public $password;
    public $ref_promo_code;
    public $using_apple;
    public $name;


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['phone', 'trim'],
            ['phone', 'required', 'except' => ['facebook-signup', 'apple-signup']],
            ['phone', 'unique', 'targetClass' => User::class, 'message' => Yii::t('api', 'Цей номер телефону вже використовується.')],
            ['phone', 'string', 'min' => 12, 'max' => 12],
            ['email', 'trim'],
            ['email', 'email', 'except' => ['apple-signup']],
            ['email', 'string', 'max' => 255, 'except' => ['apple-signup']],
            ['email', 'unique', 'targetClass' => User::class, 'message' => Yii::t('api', 'Ця адреса електронної пошти вже використовується.')],
            ['password', 'required', 'except' => ['facebook-signup', 'apple-signup']],
            ['password', 'string', 'min' => 6],
            ['ref_promo_code', 'string', 'max' => 10],
            ['ref_promo_code', 'validateRefPromoCode'],
            ['name', 'string', 'min' => 3, 'except' => ['apple-signup']]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'phone' => Yii::t('api', 'Номер телефону'),
            'email' => Yii::t('api', 'E-mail'),
            'password' => Yii::t('api', 'Пароль'),
            'ref_promo_code' => Yii::t('api', 'Промо-код друга'),
        ];
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function validateRefPromoCode($attribute, $params): void
    {
        if (!empty($this->ref_promo_code) && $this->ref_promo_code !== 'null' && $this->ref_promo_code !== 'undefined' && !$this->hasErrors()) {
            $friend = User::findByPromoCode($this->ref_promo_code);
            if (!$friend) {
                $this->addError($attribute, Yii::t('api', 'Промокод не знайдено.'));
            }
        }
    }

    /**
     * @return User|null the saved model or null if saving fails
     * @throws Exception on failure generate auth key
     */
    public function signup(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();

        $user->role = User::ROLE_USER;
        $user->status = User::STATUS_ACTIVE;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->ref_promo_code = !empty($this->ref_promo_code) ? $this->ref_promo_code : '';
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->username = '';
        $user->name = '';
        $user->avatar = '';
        $user->temp_password_hash = '';
        $user->temp_password_created_at = 0;
        $user->address = '';
        $user->promo_code = 0;

        if ($user->save(false)) {
            $user->promo_code = User::generatePromoCode($user->user_id);
            $user->save(false);

            return $user;
        }

        return null;
    }

    /**
     * @param array $data
     * @return User|null
     * @throws Exception
     */
    public function facebookSignup($data): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        /** @var User|ImageBehavior $user */
        $user = new User();

        $user->scenario = 'facebook-create';

        $user->username = !empty($data['name']) ? $data['name'] : '';
        $user->name = !empty($data['name']) ? $data['name'] : '';

        if (!empty($data['birthday'])) {
            $user->birth_date = date("Y-d-m", strtotime($data['birthday']));
        }

        $user->avatar = $user->uploadImageByUrl(!empty($data['picture']['data']['url']) ? $data['picture']['data']['url'] : null);
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->ref_promo_code = !empty($this->ref_promo_code) ? $this->ref_promo_code : '';
        $user->generateAuthKey();
        $user->facebook_id = !empty($data['id']) ? $data['id'] : '';
        $user->role = User::ROLE_USER;
        $user->status = User::STATUS_ACTIVE;
        $user->password_hash = '';
        $user->temp_password_hash = '';
        $user->temp_password_created_at = 0;
        $user->address = '';
        $user->promo_code = '';

        if ($user->save(false)) {
            $user->promo_code = User::generatePromoCode($user->user_id);
            $user->save(false);

            return $user;
        }

        return null;
    }


    /**
     * @param array $data
     * @return User|null
     * @throws Exception
     */
    public function appleSignup($data): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        /** @var User|ImageBehavior $user */
        $user = new User();

        $user->scenario = 'apple-create';
        $user->username = (!empty($data['name']) && $data['name'] !== 'null') ? $data['name'] : '-';
        $user->name = (!empty($data['name']) && $data['name'] !== 'null') ? $data['name'] : '-';
        if (!empty($data['birthday'])) {
            $user->birth_date = date("Y-d-m", strtotime($data['birthday']));
        }
        $user->avatar = $user->uploadImageByUrl(!empty($data['picture']['data']['url']) ? $data['picture']['data']['url'] : null);
        $user->email = $data['email'] ?? '';
        $user->ref_promo_code = $data['ref_promo_code'] ?? '';
        $user->phone = '';
        $user->generateAuthKey();
        $user->role = User::ROLE_USER;
        $user->status = User::STATUS_ACTIVE;
        $user->password_hash = '';
        $user->temp_password_hash = '';
        $user->temp_password_created_at = 0;
        $user->address = '';
        $user->promo_code = '';
        $user->apple_id = $data['apple_id'];

        if ($user->save(false)) {
            $user->promo_code = User::generatePromoCode($user->user_id);
            $user->save(false);

            return $user;
        }
        return null;
    }
}
