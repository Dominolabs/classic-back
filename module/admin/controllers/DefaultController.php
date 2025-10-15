<?php

namespace app\module\admin\controllers;

use Yii;
use app\module\admin\models\User;
use yii\base\InvalidParamException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use app\module\admin\models\LoginForm;
use app\module\admin\models\PasswordResetRequestForm;
use app\module\admin\models\ResetPasswordForm;

class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'request-password-reset', 'reset-password'],
                        'allow' => true,
                    ], [
                        'allow' => false,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            /** @var User $identity */
                            $identity = Yii::$app->user->identity;

                            return $identity->isUser;
                        },
                        'denyCallback' => function ($rule, $action) {
                            $this->redirect('/');
                        },
                    ], [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function goHome()
    {
        return Yii::$app->getResponse()->redirect('/admin');
    }

    /**
     * Renders the index view for the module.
     *
     * @return string index view
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string login response
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string logout response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * Requests password reset.
     *
     * @return mixed request password form
     * @throws \yii\base\Exception
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'main-login';

        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для получения дальнейших инструкций по сбросу пароля.');
            } else {
                Yii::$app->session->setFlash('error', 'Извините, не удалось сбросить пароль для указанного адреса электронной почты. Попрубуйте позже.');
            }
            return $this->goHome();
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws \yii\base\Exception
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'main-login';

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->goHome();
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Новый пароль сохранен.');
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Uploads action for CKEditor plugin.
     */
    public function actionUpload()
    {
        // Check if upload directory exists. If not exists, create it.
        $uploadDirectory = Yii::getAlias('@app/web/image/editor');

        if (!is_dir($uploadDirectory)) {
            @mkdir($uploadDirectory, 0777, true);
        }

        // Upload image
        $extension = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);

        $url = '/image/editor/' . md5(uniqid(time(), true)) . '.' . $extension;

        if (($_FILES['upload'] == "none") || (empty($_FILES['upload']['name']))) {
            $message = "Файл не загружен.";
        } else if ($_FILES['upload']["size"] == 0) {
            $message = "Файл имеет нулевую длину.";
        } else if (($_FILES['upload']["type"] != "image/pjpeg") && ($_FILES['upload']["type"] != "image/jpeg") && ($_FILES['upload']["type"] != "image/png")) {
            $message = "Изображение должно быть в формате JPG или PNG.";
        } else if (!is_uploaded_file($_FILES['upload']["tmp_name"])) {
            $message = "Возможно, вы пытаетесь взломать наш сервер. Мы к вам; ожидайте стук в дверь в ближайшее время.";
        } else {
            $message = "";
            $move = @move_uploaded_file($_FILES['upload']['tmp_name'], \Yii::getAlias('@webroot') . $url);

            if (!$move) {
                $message = "Ошибка при перемещении загруженного файла. Проверьте, разрешен ли доступ на чтение/запись/изменение.";
            }

            $url = Url::to($url);
        }

        $funcNum = Yii::$app->request->get('CKEditorFuncNum');

        echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
    }
}
