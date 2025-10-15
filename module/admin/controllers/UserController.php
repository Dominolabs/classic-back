<?php

namespace app\module\admin\controllers;

use Yii;
use app\components\ImageBehavior;
use app\module\admin\models\User;
use app\module\admin\models\UserSearch;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class UserController extends Controller
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
                        'allow' => false,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            /** @var User $identity */
                            $identity = Yii::$app->user->identity;

                            return $identity->isUser || $identity->isAdminHotel;
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed create view
     * @throws \yii\base\Exception on bad password parameter
     */
    public function actionCreate()
    {
        /** @var User|ImageBehavior $model */
        $model = new User();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post())) {
            $newPassword = isset($_POST['User']['newPassword']) ? $_POST['User']['newPassword'] : null;
            $model->role = isset($_POST['User']['role']) ? $_POST['User']['role'] : User::ROLE_ADMIN;
            if (!empty($newPassword)) {
                $model->setPassword($newPassword);
            }
            $isValid = $model->validate();
            $model->avatarFile = UploadedFile::getInstance($model, 'avatarFile');
            if ($model->avatarFile !== null) {
                $model->avatar = $model->uploadImage();
            }
            $isValid = $model->validate('image') && $isValid;
            if (!empty($model->birth_date)) {
                $model->birth_date = Yii::$app->formatter->asDate($model->birth_date, 'yyyy-MM-dd');
            }
            if ($isValid && $model->save(false)) {
                $model->promo_code = User::generatePromoCode($model->user_id);
                $model->save(false);
                return $this->goBack();
            }
        }
        $placeholder = ImageBehavior::placeholder(100, 100);
        return $this->render('create', [
            'model' => $model,
            'placeholder' => $placeholder
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id user id
     * @return mixed update view
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \yii\base\Exception on bad password parameter
     */
    public function actionUpdate($id)
    {
        /** @var User|ImageBehavior $model */
        $model = $this->findModel($id);
        $model->scenario = 'update';
        if ($model->load(Yii::$app->request->post())) {
            $newPassword = $_POST['User']['newPassword'];
            $model->role = $_POST['User']['role'];
            $newAvatarFile = UploadedFile::getInstance($model, 'avatarFile');
            if ($newAvatarFile !== null) {
                $model->removeImage($model->avatar); // Remove old image
                $model->avatarFile = $newAvatarFile;
                $isValid = $model->validate();
                $model->avatar = $model->uploadImage('avatarFile');
            } else {
                $isValid = $model->validate();
            }
            if (!empty($newPassword)) {
                $model->setPassword($newPassword);
            }
            if (!empty($model->birth_date)) {
                $model->birth_date = Yii::$app->formatter->asDate($model->birth_date, 'yyyy-MM-dd');
            }

            if ($isValid && $model->save(false)) {
                return $this->goBack();
            }
        }
        $placeholder = ImageBehavior::placeholder(100, 100);
        return $this->render('update', [
            'model' => $model,
            'placeholder' => $placeholder
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception|\Throwable in case delete failed.
     * @throws \yii\db\StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     */
    public function actionDelete($id)
    {
        /** @var User|ImageBehavior $user */
        $user = $this->findModel($id);

        if (User::getAllCount() > 1) {
            $user->removeImage($user->avatar);
            $user->delete();
        }

        return $this->goBack();
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id user id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }
}
