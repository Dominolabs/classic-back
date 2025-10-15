<?php

namespace app\module\api\controllers;

use app\module\admin\module\order\models\City;
use app\module\admin\module\order\models\Order;
use Yii;
use app\components\ImageBehavior;
use app\models\ChangePasswordForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\LoginForm;
use app\models\SignupForm;
use app\module\admin\models\User;
use app\module\admin\models\UserNotificationsHistory;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\web\UploadedFile;

class UserController extends BaseApiController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors[] = [
            'class' => VerbFilter::class,
            'actions' => [
                'signup'                            => ['POST'],
                'login'                             => ['POST'],
                'request-password-reset'            => ['POST'],
                'reset-password'                    => ['POST'],
                'change-password'                   => ['POST'],
                'settings'                          => ['GET', 'POST'],
                'set-device'                        => ['POST'],
                'notifications-history'             => ['GET'],
                'set-notification-status-as-read'   => ['POST'],
                'last-order'                        => ['GET'],
                'orders-history'                    => ['GET'],
                'device-app'                        => ['POST'],
                'delete-user'                       => ['DELETE'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @param string $lang
     * @return array response data
     * @throws \yii\base\Exception on failure generate auth key
     */
    public function actionSignup($lang): array
    {
        Yii::$app->language = $lang;

        $model = new SignupForm();
        $model->attributes = Yii::$app->request->post();
        $model->phone = preg_replace('/\D+/', '', $model->phone);

        if ($user = $model->signup()) {
            if ($user = User::findByPhone($user->phone)) {
                return [
                    'status' => 'success',
                    'data' => $user
                ];
            }

            Yii::$app->response->statusCode = 422;

            return [
                'status' => 'error',
                'message' => Yii::t('api', 'Невірний номер телефону або пароль.'),
            ];
        }

        Yii::$app->response->statusCode = 422;

        $response = [
            'status' => 'error',
        ];
        foreach ($model->getErrors() as $attribute => $errors) {
            $response['errors'][$attribute] = $errors[0];
        }

        return $response;
    }

    /**
     * @param string $lang
     * @return array
     * @throws \yii\base\Exception
     */
    public function actionLogin($lang): array
    {
        Yii::$app->language = $lang;

        if (!empty(Yii::$app->request->post('using_apple'))) {
            $post_data = Yii::$app->request->post();
            return $this->appleLogin($post_data);
        }

        $model = new LoginForm();
        $model->attributes = Yii::$app->request->post();
        $model->phone = preg_replace('/\D+/', '', $model->phone);

        if (!empty($model->token)) {
            $data = $this->fetchFacebookData($model->token);
            if ($data) {
                return $this->facebookLogin($data['id'], $data);
            }
        }

        if ($model->validate() && $user = User::findByPhone($model->phone)) {
            return [
                'status' => 'success',
                'data' => $user
            ];
        }

        Yii::$app->response->statusCode = 422;

        return [
            'status' => 'error',
            'message' => Yii::t('api', 'Невірний номер телефону або пароль.'),
        ];
    }

    /**
     * @param string $lang
     * @return array
     * @throws \yii\base\Exception
     */
    public function actionRequestPasswordReset($lang): array
    {
        Yii::$app->language = $lang;

        $model = new PasswordResetRequestForm();
        $model->attributes = Yii::$app->request->post();

        if ($model->validate()) {
            if ($model->sendEmail($model->type)) {
                return [
                    'status' => 'success',
                    'message' => Yii::t('api', 'Перевірте свою електронну пошту для отримання подальших інструкцій щодо скидання паролю.')
                ];
            }

            Yii::$app->response->statusCode = 503;

            return [
                'status' => 'error',
                'message' => Yii::t('api', 'Вибачте, не вдалося скинути пароль для вказаної адреси електронної пошти. Спробуйте пізніше.')
            ];
        }

        Yii::$app->response->statusCode = 422;

        $response = [
            'status' => 'error',
        ];
        foreach ($model->getErrors() as $attribute => $errors) {
            $response['errors'][$attribute] = $errors[0];
        }

        return $response;
    }

    /**
     * @param string $lang
     * @return array
     * @throws \yii\base\Exception
     */
    public function actionResetPassword($lang): array
    {
        Yii::$app->language = $lang;

        $model = new ResetPasswordForm();
        $model->attributes = Yii::$app->request->post();

        $model->password_reset_token = trim($model->password_reset_token, '/');
        $user = User::findByPasswordResetToken($model->password_reset_token);

        if ($user) {
            if ($model->validate() && $model->resetPassword($user)) {
                return [
                    'status' => 'success',
                    'message' => Yii::t('api', 'Пароль успішно змінений.')
                ];
            }

            Yii::$app->response->statusCode = 422;

            $response = [
                'status' => 'error',
            ];
            foreach ($model->getErrors() as $attribute => $errors) {
                $response['errors'][$attribute] = $errors[0];
            }

            return $response;
        }

        Yii::$app->response->statusCode = 422;

        return [
            'status' => 'error',
            'message' => Yii::t('api', 'Невірний токен скидання паролю.')
        ];
    }

    /**
     * @param string $lang
     * @return array
     * @throws \yii\base\Exception
     */
    public function actionChangePassword($lang): array
    {
        Yii::$app->language = $lang;

        /* @var User|ImageBehavior $user */
        if ($user = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')])) {
            $model = new ChangePasswordForm($user->user_id);
            $model->attributes = Yii::$app->request->post();

            if ($model->validate() && $model->changePassword()) {
                return [
                    'status' => 'success',
                    'message' => Yii::t('api', 'Пароль успішно змінений.')
                ];
            }

            Yii::$app->response->statusCode = 422;

            $response = [
                'status' => 'error',
            ];
            foreach ($model->getErrors() as $attribute => $errors) {
                $response['errors'][$attribute] = $errors[0];
            }

            return $response;
        }

        Yii::$app->response->statusCode = 422;

        return [
            'status' => 'error',
            'message' => Yii::t('api', 'Запитуваний користувач не знайдений.')
        ];
    }

    /**
     * @return array
     */
    public function actionSettings(): array
    {
        $lang = Yii::$app->request->get('lang');
        Yii::$app->language = $lang;

        /* @var User|ImageBehavior $user */
        $user = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);
        if(Yii::$app->request->post() && strlen(Yii::$app->request->post()['phone']) != 12) {
            Yii::$app->response->statusCode = 403;
            return [
                'status' => 'validation error',
                'message' => 'Номер телефону повинен містити 12 символів.'
            ];
        }

        if ($user !== null) {
            $user->scenario = 'change-settings';
            if (Yii::$app->request->isPost) {
                $user->attributes = Yii::$app->request->post();
                $user->username = $user->name;
                $user->phone = preg_replace('/\D+/', '', $user->phone);

                $validatePhone = User::where(['phone' => preg_replace('/\D+/', '', Yii::$app->request->post()['phone'])])->one();

                if($validatePhone && ($validatePhone->user_id != $user->user_id)) {
                    Yii::$app->response->statusCode = 422;
                    return [
                        'status' => 'validation error',
                        'message' => Yii::t('api', 'Цей номер телефону вже використовується.')
                    ];
                }

                if (!empty(Yii::$app->request->post('birth_date'))) {
                    $user->birth_date = date('Y-m-d', (int)$user->birth_date);
                }

                $newAvatarFile = UploadedFile::getInstanceByName('avatarFile');

                if ($newAvatarFile !== null) {
                    $user->removeImage($user->avatar); // Remove old image
                    $user->avatarFile = $newAvatarFile;
                    $isValid = $user->validate();
                    $user->avatar = $user->uploadImage('avatarFile');
                } else {
                    $isValid = $user->validate();
                }

                if ($isValid && $user->save(false)) {
                    return [
                        'status' => 'success',
                        'message' => Yii::t('user', 'Персональні дані успішно оновлені.'),
                        'user' => $user,
                    ];
                }
                Yii::$app->response->statusCode = 422;

                $response = [
                    'status' => 'error',
                ];
                foreach ($user->getErrors() as $attribute => $errors) {
                    $response['errors'][$attribute] = $errors[0];
                }

                return $response;
            }

            return [
                'status' => 'success',
                'data' => $user
            ];
        }

        Yii::$app->response->statusCode = 422;

        return [
            'status' => 'error',
            'message' => Yii::t('api', 'Запитуваний користувач не знайдений.')
        ];
    }

    /**
     * @param string $lang
     * @return array
     * @throws \yii\base\Exception
     */
    public function actionSetDevice($lang): array
    {
        Yii::$app->language = $lang;

        $deviceId = Yii::$app->request->post('device_id');
        $model = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);

        if ($model) {
            if ($deviceId) {
                $this->updateDeviceId($deviceId, $model->user_id);
            }
            return [
                'status' => 'success',
                'message' => Yii::t('api', 'Device ID оновлено.')
            ];
        }

        Yii::$app->response->statusCode = 422;

        return [
            'status' => 'error',
            'message' => Yii::t('api', 'Запитуваний користувач не знайдений.')
        ];
    }

    /**
     * @param string $lang
     * @return array response data
     */
    public function actionNotificationsHistory($lang)
    {
        Yii::$app->language = $lang;

        /* @var User|ImageBehavior $user */
        $user = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);

        if ($user !== null) {
            $dataProvider = new ActiveDataProvider([
                'query' => UserNotificationsHistory::find()
                    ->where(['user_id' => $user->user_id])
                    ->orderBy('created_at DESC')
                    ->limit(20),
                'pagination' => false,
            ]);

            $result = [];

            foreach ($dataProvider->getModels() as $model) {
                $item = [];

                $item['id'] = $model['user_notifications_history_id'];
                $item['title'] = $model['header'];

                $message = json_decode($model['message'], true);

                if (!empty($message['text'])) {
                    $item['text'] = $message['text'];
                } else {
                    $item['text'] = $model['message'];
                }

                if (!empty($message['status'])) {
                    $item['status'] = $message['status'];
                }

                if (!empty($message['status_text'])) {
                    $item['status_text'] = $message['status_text'];
                }

                $item['date'] = $model['created_at'];
                $item['read'] = $model['status'];

                if (!empty($message['restaurant'])) {
                    $item['restaurant'] = $message['restaurant'];
                }

                $result[] = $item;
            }
            return [
                'status' => 'success',
                'data' => $result
            ];
//            return [
//                'status' => 'success',
//                'data' => $dataProvider->getModels()
//            ];
        }

        Yii::$app->response->statusCode = 422;

        return [
            'status' => 'error',
            'message' => Yii::t('api', 'Запитуваний користувач не знайдений.')
        ];
    }


    /**
     * @param $string
     * @return bool
     */
    private function isJson($string)
    {
        json_decode($string, true);
        return (json_last_error() === JSON_ERROR_NONE);
    }


    /**
     * @param string $lang
     * @return array response data
     */
    public function actionSetNotificationStatusAsRead($lang)
    {
        Yii::$app->language = $lang;

        /* @var User|ImageBehavior $user */
        $user = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);

        if ($user !== null) {
            $userNotificationHistory = UserNotificationsHistory::findOne(Yii::$app->request->post('user_notification_history_id'));

            if ($userNotificationHistory) {
                $userNotificationHistory->status = UserNotificationsHistory::STATUS_READ;
                $userNotificationHistory->save(false);
            }

            return [
                'status' => 'success',
                'data' => $userNotificationHistory
            ];
        }

        Yii::$app->response->statusCode = 422;

        return [
            'status' => 'error',
            'message' => Yii::t('api', 'Запитуваний користувач не знайдений.')
        ];
    }

    /**
     * @param string $lang language code
     * @return array response data
     * @throws \yii\base\Exception on failure generate auth key
     */
    public function actionNotifications($lang)
    {
        Yii::$app->language = $lang;

        $notificationsNews = (int)Yii::$app->request->post('notifications_news');
        $notificationsDelivery = (int)Yii::$app->request->post('notifications_delivery');

        $model = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);

        if ($model) {
            $model->notifications_news = ($notificationsNews === 'true') ? 1 : 0;
            $model->notifications_delivery = ($notificationsDelivery === 'true') ? 1 : 0;
            $model->save(false);

            return [
                'status' => 'success'
            ];
        }

        return [
            'status' => 'error',
            'code' => 523,
            'message' => Yii::t('product', 'Запитуваний користувач не знайдений.')
        ];
    }

    /**
     * @param string $lang
     * @return array
     */
    public function actionLastOrder($lang): array
    {
        Yii::$app->language = $lang;

        /* @var User|ImageBehavior $user */
        $user = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);

        if ($user !== null) {
            $result = [];
            /** @var Order|null $order */
            $order = Order::find()->where(['user_id' => $user->user_id])->orderBy('order_id DESC')->one();
            if (empty($order)) {
                return [
                    'status' => 'success',
                    'data' => []
                ];
            }
            $city = City::findOne($order->city_id);

            if ($order) {
                $result['city'] = $city;
                $result['street'] = $order->street;
                $result['entrance'] = $order->entrance;
                $result['house_number'] = $order->house_number;
                $result['apartment_number'] = $order->apartment_number;
                $result['do_not_call'] = $order->do_not_call;
                $result['have_a_child'] = $order->have_a_child;
                $result['have_a_dog'] = $order->have_a_dog;
                $result['call_me_back'] = $order->call_me_back;
            }

            return [
                'status' => 'success',
                'data' => $result
            ];
        }

        return [
            'status' => 'error',
            'message' => Yii::t('api', 'Запитуваний користувач не знайдений.')
        ];
    }

    /**
     * @param string $facebookId
     * @param array $data
     * @return array
     * @throws \yii\base\Exception
     */
    protected function facebookLogin($facebookId, array $data = []): array
    {
        $user = User::findByFacebookId($facebookId);

        if ($user !== null) {
            return [
                'status' => 'success',
                'data' => $user
            ];
        }
        if (!empty($data['email'])) {
            $user = User::findByEmail($data['email']);
            if ($user !== null) {
                return [
                    'status' => 'success',
                    'data' => $user
                ];
            }
        }
        $model = new SignupForm();
        $model->scenario = 'facebook-signup';
        $model->phone = '';
        $model->email = !empty($data['email']) ? $data['email'] : '';
        $model->ref_promo_code = Yii::$app->request->post('ref_promo_code');
        /** @var User|ImageBehavior $user */
        if ($user = $model->facebookSignup($data)) {
            return $this->facebookLogin($user->facebook_id, []);
        }

        Yii::$app->response->statusCode = 422;

        $response = [
            'status' => 'error',
        ];
        foreach ($model->getErrors() as $attribute => $errors) {
            $response['errors'][$attribute] = $errors[0];
        }

        return $response;
    }



    protected function appleLogin($request)
    {
        try {
            $apple_id = $request['apple_id'];
            $email = $request['email'];

            $user = User::findByEmail($email) ?: User::findByAppleId($apple_id);

            if ($user !== null) {
                if (empty($user->apple_id)) {
                    $user->apple_id = $apple_id;
                    $user->save();
                }
                return [
                    'status' => 'success',
                    'data' => $user
                ];
            }
            $model = new SignupForm();
            $model->scenario = 'apple-signup';
            $model->phone = '';
            $model->email = $request['email'] ?? '';
            $model->name = $request['name'] ?? '';

            if ($user = $model->appleSignup($request)) {
                return [
                    'status' => 'success',
                    'data' => $user
                ];
            }
            Yii::$app->response->statusCode = 422;

            $response = [
                'status' => 'error',
            ];
            foreach ($model->getErrors() as $attribute => $errors) {
                $response['errors'][$attribute] = $errors[0];
            }

            return $response;
        } catch (\Throwable $e) {
            return $this->errorResponseHandler($e);
        }
    }



    /**
     * @param \Throwable $exception
     * @return array
     */
    protected function errorResponseHandler(\Throwable $exception): array
    {
        $error = $this->formErrorForLogging($exception);
        Yii::error($error, 'api');
        $response = [
            'status' => 'error',
            'error' => 'Internal server error',
            'message' => $exception->getMessage()
        ];
        Yii::$app->response->statusCode = 500;
        return $response;
    }

    /**
     * @param string $lang
     * @return array response data
     */
    public function actionDeleteUser($lang)
    {
        $user = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);
        $user->delete();

        return [
            'status' => 200,
            'message' => 'Користувач успішно видалений!'
        ];
    }

    /**
     * @param string $lang
     * @return array response data
     */
    public function actionDeviceApp($lang)
    {
        Yii::$app->language = $lang;
        Yii::error('TEST123');
        $token = Yii::$app->request->headers->get('payload');

        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);
        $events = json_decode(json_decode($tokenPayload)->events);
        $user = User::where('apple_id', $events->sub)->first();
        if ($user != null) {
            if ($events->type == 'consent-revoked') {
                $user->apple_id = null;
                $user->save();
            } elseif ($events->type == 'account-delete') {
                $user->delete();
            }
        }

        return [
            'status' => 'error',
            'message' => Yii::t('product', 'Запитуваний користувач не знайдений.')
        ];
    }


    /**
     * @param \Throwable $exception
     * @return array
     */
    protected function formErrorForLogging(\Throwable $exception): array
    {
        return [
            'url' => Yii::$app->request->absoluteUrl,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage()
        ];
    }



    /**
     * Updates user device id.
     * @param string $deviceId device id
     * @param int $userId user id
     * @throws \yii\db\Exception
     */
    protected function updateDeviceId($deviceId, $userId)
    {
        if (!empty($deviceId)) {
            Yii::$app->db->createCommand('UPDATE ' . User::tableName() . " SET device_id = '" . $deviceId . "' WHERE user_id = $userId")->execute();
            Yii::$app->db->createCommand('UPDATE ' . User::tableName() . " SET device_id = NULL WHERE device_id = '" . $deviceId . "' AND user_id != $userId")->execute();
        }
    }

    /**
     * @param string $token
     * @return bool|mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    protected function fetchFacebookData($token)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://graph.facebook.com/v3.1/me?fields=id,email,name,birthday,picture.width(200).height(200)&access_token=' . $token)
            ->send();
        if ($response->isOk) {
            return $response->data;
        }

        return false;
    }
}
