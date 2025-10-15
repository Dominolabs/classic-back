<?php

namespace app\module\admin\models;

use app\components\ExpoNotifications;
use app\module\admin\module\order\models\Order;
use app\module\api\controllers\BaseApiController;
use app\module\api\module\viber\models\ActiveModel;
use Throwable;
use Yii;
use Imagine\Image\ManipulatorInterface;
use app\components\ImageBehavior;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * @property int $user_id
 * @property string $username
 * @property string $name
 * @property string $birth_date
 * @property string $avatar
 * @property string $auth_key
 * @property string $password_hash
 * @property string $temp_password_hash
 * @property string $temp_password_created_at
 * @property string $password_reset_token
 * @property string $password_reset_code
 * @property string $facebook_id
 * @property string $apple_id
 * @property string $device_id
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $promo_code
 * @property string $ref_promo_code
 * @property int $notifications_news
 * @property int $notifications_delivery
 * @property int $role
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property int $send_emails
 *
 * @property string $password write-only password
 * @property string $isSuperAdmin
 * @property string $isAdmin
 * @property string $isAdminHotel
 * @property string $isUser
 * @property int $ordersCount
 */
class User extends ActiveModel implements IdentityInterface
{
    /**
     * @var string
     */
    public $newPassword;

    /**
     * @var UploadedFile
     */
    public $avatarFile;

    public const ROLE_SUPER_ADMIN = 0;
    public const ROLE_ADMIN = 1;
    public const ROLE_USER = 2;
    public const ROLE_ADMIN_HOTEL = 3;

    public const STATUS_NOT_ACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            'avatar' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'user',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role'], 'safe'],
            [['email', 'role', 'status'], 'required', 'except' => ['apple-create']],
            [['phone', 'password_hash'], 'required', 'except' => ['update', 'facebook-create', 'change-settings', 'apple-create']],
            [['newPassword'], 'required', 'on' => 'create'],
            ['newPassword', 'string', 'min' => 6],
            [['birth_date'], 'safe'],
            [['birth_date'], 'default', 'value' => null],
            [['address'], 'string', 'max' => 5000],
            [['email'], 'email', 'except' => ['apple-create']],
            [['status', '!role', 'notifications_news', 'notifications_delivery'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            ['role', 'default', 'value' => self::ROLE_ADMIN],
            ['role', 'in', 'range' => [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_USER, self::ROLE_ADMIN_HOTEL]],
            [['temp_password_created_at', 'created_at', 'updated_at', 'role'], 'safe'],
            [
                [
                    'username',
                    'name',
                    'avatar',
                    'password_hash',
                    'temp_password_hash',
                    'password_reset_token',
                    'password_reset_code',
                    'device_id',
                    'email',
                    'newPassword',
                    'facebook_id',
                    'apple_id'
                ],
                'string',
                'max' => 255,
                'except' => ['apple-create']
            ],
            [['auth_key'], 'string', 'max' => 32],
            [['promo_code', 'ref_promo_code'], 'string', 'max' => 10],
            [['ref_promo_code'], 'default', 'value' => ''],
            [['auth_key'], 'default', 'value' => Yii::$app->security->generateRandomString()],
            [['phone',], 'unique', 'except' => ['facebook-create', 'apple-create']],
            [['email', 'facebook_id', 'password_reset_token', 'password_reset_code'], 'unique', 'except' => ['apple-create']],
            [
                ['avatarFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg, gif, svg',
                'maxSize' => 1024 * 1024 * 20   // 20 Mb
            ],
            ['send_emails', 'default', 'value' => 1],
            ['send_emails', 'in', 'range' => [0, 1]],
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id' => 'user_id',
            'name',
            'birth_date',
            'facebook_id',
            'avatar' => function() {
                if (!empty($this->avatar) && file_exists($this->getImagePath() . DIRECTORY_SEPARATOR . $this->avatar)) {
                    return BaseApiController::BASE_SITE_URL . 'image/user/' . $this->avatar;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'auth_key',
            'device_id',
            'email',
            'phone',
            'address',
            'promo_code',
            'ref_promo_code',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * Finds user by username.
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email.
     *
     * @param string $email user email
     * @return static|null User instance
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by phone number.
     *
     * @param string $phone user phone
     * @return static|null User instance
     */
    public static function findByPhone($phone)
    {
        return static::findOne(['phone' => $phone, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token.
     *
     * @param string $token password reset token
     * @return static|null user data
     */
    public static function findByPasswordResetToken($token): ?User
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * @param string $promoCode
     * @return static|null
     */
    public static function findByPromoCode($promoCode): ?User
    {
        return static::findOne(['promo_code' => $promoCode]);
    }

    /**
     * Finds user by Facebook user id.
     *
     * @param string $facebookId Facebook user id
     * @return static|null User instance
     */
    public static function findByFacebookId($facebookId)
    {
        return static::findOne(['facebook_id' => $facebookId, 'status' => self::STATUS_ACTIVE]);
    }


    public static function findByAppleId($appleId)
    {
        return static::findOne(['apple_id' => $appleId, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @param string $token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token): bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password.
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Validates temporary password.
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validateTemporaryPassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->temp_password_hash);
    }

    /**
     * Generates password reset code hash from password reset code and sets it to the model
     *
     * @param string $code password reset code to set
     * @throws Exception on bad password parameter
     * @throws Exception
     */
    public function setPasswordResetCode($code): void
    {
        $this->password_reset_code = Yii::$app->security->generatePasswordHash($code);
    }

    /**
     * Validates password reset code.
     *
     * @param string $code code to validate
     * @return bool if code provided is valid for current user
     */
    public function validatePasswordResetCode($code): bool
    {
        if (!empty($this->password_reset_code)) {
            return Yii::$app->security->validatePassword($code, $this->password_reset_code);
        }

        return false;
    }

    /**
     * @param string $password password to set
     * @throws Exception on bad password parameter
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @throws Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates temporary password.
     *
     * @return string generated temporary password
     * @throws Exception on bad password parameter or cost parameter.
     */
    public function generateTemporaryPassword()
    {
        $password = Yii::$app->security->generateRandomString(8);
        $this->temp_password_hash = Yii::$app->security->generatePasswordHash($password);
        $this->temp_password_created_at = time();

        return $password;
    }

    /**
     * Removes password reset token.
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Removes temporary password.
     */
    public function removeTemporaryPassword()
    {
        $this->temp_password_hash = '';
        $this->temp_password_created_at = 0;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'ID пользователя',
            'username' => Yii::t('user', 'Имя пользователя'),
            'name' => Yii::t('user', 'Полное имя'),
            'birth_date' => 'Дата рождения',
            'avatar' => 'Аватар',
            'avatarFile' => 'Аватар',
            'auth_key' => 'Auth Key',
            'newPassword' => Yii::t('user', 'Пароль'),
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'password_reset_code' => 'Password Reset Code',
            'email' => Yii::t('user', 'E-mail'),
            'phone' => Yii::t('user', 'Номер телефона'),
            'address' => Yii::t('user', 'Адрес'),
            'promo_code' => Yii::t('user', 'Промо-код'),
            'ref_promo_code' => Yii::t('user', 'Промо-код друга'),
            'device_id' => 'Device ID',
            'role' => 'Роль',
            'status' => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return bool whether user role is 'Super Admin'
     */
    public function getIsSuperAdmin()
    {
        return $this->role == self::ROLE_SUPER_ADMIN;
    }

    /**
     * @return bool whether user role is 'Admin'
     */
    public function getIsAdmin()
    {
        return $this->role == self::ROLE_ADMIN;
    }

    /**
     * @return bool whether user role is 'AdminHotel'
     */
    public function getIsAdminHotel()
    {
        return $this->role == self::ROLE_ADMIN_HOTEL;
    }

    /**
     * @return bool whether user role is 'User'
     */
    public function getIsUser()
    {
        return $this->role == self::ROLE_USER;
    }

    /**
     * Returns roles list.
     *
     * @return array roles list data
     */
    public static function getRolesList()
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_USER => 'User',
            self::ROLE_ADMIN_HOTEL => 'Admin Hotel',
        ];
    }

    /**
     * Returns role name by specified role constant.
     *
     * @param integer $role role constant
     * @return mixed|string role name
     */
    public static function getRoleName($role)
    {
        $roles = self::getRolesList();

        return isset($roles[$role]) ? $roles[$role] : Yii::t('app', 'Неопределено');
    }

    /**
     * Returns statuses list.
     *
     * @return array statuses list data
     */
    public static function getStatusesList()
    {
        return [
            self::STATUS_ACTIVE => 'Включено',
            self::STATUS_NOT_ACTIVE => 'Отключено'
        ];
    }

    /**
     * Returns status name by specified status constant.
     *
     * @param integer $status status constant
     * @return mixed|string status name
     */
    public static function getStatusName($status)
    {
        $statuses = self::getStatusesList();
        return isset($statuses[$status]) ? $statuses[$status] : 'Неопределено';
    }

    /**
     * Returns all User models count.
     *
     * @return int|string User models count
     */
    public static function getAllCount()
    {
        return self::find()->count();
    }

    /**
     * Returns users list with role 'user'.\
     *
     * @return array users list
     */
    public static function getUsersList()
    {
        $result = [];

        $users = self::getAllByRole(self::ROLE_USER);

        foreach ($users as $user) {
            $result[$user['user_id']] = $user['username'];
        }

        return $result;
    }

    /**
     * Returns all user with specified role.
     *
     * @param int $role user role
     * @return array users list
     */
    public static function getAllByRole($role)
    {
        return (new Query())
            ->select([
                'user_id',
                'username',
                'name',
                'birth_date',
                'avatar',
                'email',
                'phone',
                'address',
                'role',
                'promo_code',
                'ref_promo_code',
                'status',
                'created_at',
                'updated_at'
            ])
            ->from(self::tableName())
            ->where(['role' => $role])
            ->orderBy('username ASC')
            ->all();
    }

    /**
     * Returns user data by specified user id.
     *
     * @param int $userId user id
     * @return array|bool user data
     */
    public static function getById($userId)
    {
        return (new Query())
            ->select([
                'user_id',
                'username',
                'name',
                'birth_date',
                'avatar',
                'email',
                'phone',
                'address',
                'role',
                'promo_code',
                'ref_promo_code',
                'status',
                'created_at',
                'updated_at'
            ])
            ->from(self::tableName())
            ->where(['user_id' => $userId])
            ->one();
    }

    /**
     * Generates User promo code.
     *
     * @param int $userId user ID
     * @return string generated User promo code
     */
    public static function generatePromoCode($userId)
    {
        return sprintf('%05d', $userId);
    }

    /**
     * Returns image URL.
     *
     * @param string $filename image filename
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @param string $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100). Defaults 100.
     * @return null|string image URL
     */
    public static function getImageUrl(
        $filename,
        $width,
        $height,
        $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND,
        $quality = 100
    ) {
        return (new self())->resizeImage($filename, $width, $height, $mode, $quality);
    }

    /**
     * Returns original image path.
     *
     * @param string $filename image filename
     * @return string image path
     */
    public static function getOriginalImagePath($filename)
    {
        return (new self())->getImagePath() . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * @param null|string $header
     * @param string $message
     * @param string|array $recipients
     */
    public static function sendExpoNotification($header, $message, $recipients)
    {
        try {
            $notification = ['body' => $message];

            if ($header) {
                $notification['title'] = $header;
            }

            $expoNotifications = new ExpoNotifications();

            if (!empty($recipients)) {
                $result = $expoNotifications->notify($notification, $recipients);
                Yii::info('Expo returns: ' . json_encode($result), 'notification');
            }
        } catch (Throwable $e) {
            Yii::error('Failed to send expo notification. Error: ' . $e->getMessage(), 'notification');
        }
    }

    /**
     * @param string $header
     * @param string $message
     * @param array $recipients
     * @return bool
     */
    public static function sendEmail($header, $message, $recipients)
    {
        try {
            $headers = "X-Mailer:USER_AGENT_MOZILLA_XM\r\n";
            $headers .= "User-Agent:USER_AGENT_MOZILLA_UA\r\n";
            $headers .= "Content-type:text/html\r\n";
            $headers .= "From: " . Yii::$app->name . ' робот' . " <".Yii::$app->params['supportEmail']. ">\r\n";
            $link_text = "\n<br>" . Yii::t('common', "If you don't want receive more letter click on the link below.") . "\n";
            foreach ($recipients as $id => $email) {
                $link = Url::to("unsubscribe?user=$id", 'https');
                mail($email, $header, $message . $link_text . $link, $headers);
            }



//            return Yii::$app
//                ->mailer
//                ->compose(
//                    ['html' => 'mailing-html', 'text' => 'mailing-text'],
//                    [
//                        'message' => $message,
//                    ]
//                )
//                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' робот'])
//                ->setBcc($recipients)
//                ->setSubject($header)
//                ->send();
        } catch (Throwable $e) {
            Yii::error('Failed to send mailing. Error: ' . $e->getMessage(), 'emails');
        }
    }

    /**
     * Finds all users by birthday.
     * @param string $birthday birthday in format "d.m" (day.month)
     * @return array users list
     */
    public static function findByBirthday($birthday)
    {
        $birthdayParts = explode('.', $birthday);
        return (new Query())
            ->select([
                'user_id',
                'username',
                'name',
                'birth_date',
                'device_id',
                'avatar',
                'email',
                'phone',
                'address',
                'role',
                'promo_code',
                'ref_promo_code',
                'status',
                'created_at',
                'updated_at'
            ])
            ->from(self::tableName())
            ->where('role = ' . self::ROLE_USER . ' AND DAY(birth_date) = ' . $birthdayParts[0] . ' AND MONTH(birth_date) = ' . $birthdayParts[1])
            ->all();
    }


    /**
     * @param string $header
     * @param string $message
     * @param array $userIds
     */
    public static function addToNotificationsHistory($header, $message, $userIds)
    {
        try {
            $bulkInsertArray = [];
            $columnNameArray = ['user_id', 'header', 'message', 'status', 'created_at', 'updated_at'];

            foreach ($userIds as $userId) {
                $bulkInsertArray[] = [
                    'user_id' => $userId,
                    'header' => $header,
                    'message' => json_encode($message),
                    'status' => UserNotificationsHistory::STATUS_NOT_READ,
                    'created_at' => time(),
                    'updated_at' => time(),
                ];
            }

            Yii::$app->db->createCommand()
                ->batchInsert(UserNotificationsHistory::tableName(), $columnNameArray, $bulkInsertArray)
                ->execute();
        } catch (Throwable $e) {
            Yii::error('Failed to save notifications history. Error: ' . $e->getMessage(), 'notification');
        }
    }

    /**
     * @return ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['user_id' => 'user_id']);
    }


    /**
     * @return int|string
     */
    public function getOrdersCount()
    {
        return $this->getOrders()->count();
    }

    public function setOrdersCount()
    {
    }

    public function getFullName()
    {
        return $this->name ?? '';
    }
}
