<?php

namespace app\module\admin\controllers;

use app\module\admin\models\NotificationForm;
use app\module\admin\models\NotificationsHistory;
use app\module\admin\models\NotificationsHistorySearch;
use app\module\admin\models\Promocode;
use app\module\admin\models\PromocodeToCategory;
use Exception;
use Throwable;
use Yii;
use app\module\admin\models\User;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class NotificationsHistoryController extends Controller
{
    /**
     * @return array
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
                        'matchCallback' => static function ($rule, $action) {
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new NotificationsHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new NotificationForm();

        if ($model->load(Yii::$app->request->post())) {
            $notificationsHistoryModel = new NotificationsHistory();
            $notificationsHistoryModel->header = $model->header;
            $notificationsHistoryModel->message = $model->message;

            if ($model->send() && $notificationsHistoryModel->save()) {
                Yii::$app->session->setFlash('success', 'Уведомление успешно отправлено.');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось отправить уведомление.');
            }

            return $this->goBack();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id model id
     * @return mixed update view
     * @throws Exception|Throwable in case delete failed being deleted is outdated.
     * @throws NotFoundHttpException if model not found
     */
    public function actionUpdate($id)
    {
        /** @var NotificationsHistory $model */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $isValid = $model->validate();

            if ($isValid && $model->save(false)) {
                return $this->goBack();
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id model id
     * @return mixed response object
     * @throws NotFoundHttpException if the model cannot be found
     * @throws Exception|Throwable in case delete failed.
     * @throws StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->goBack();
    }

    /**
     * @param integer $id model id
     * @return NotificationsHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NotificationsHistory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
