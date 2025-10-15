<?php

namespace app\module\admin\module\feedback\controllers;

use Yii;
use app\components\ImageBehavior;
use app\module\admin\models\User;
use app\module\admin\module\feedback\models\Feedback;
use app\module\admin\module\feedback\models\FeedbackSearch;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class FeedbackController extends Controller
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
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all feedback models.
     *
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new FeedbackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing feedback model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id model id
     * @throws NotFoundHttpException if model not found
     * @throws \Exception|\Throwable in case delete failed
     * being deleted is outdated.
     * @return mixed update view
     */
    public function actionUpdate($id)
    {
        /** @var feedback|ImageBehavior $model */
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
     * Deletes an existing feedback model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id model id
     * @return mixed response object
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception|\Throwable in case delete failed.
     * @throws \yii\db\StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     */
    public function actionDelete($id)
    {
        /** @var feedback|ImageBehavior $model */
        $model =  $this->findModel($id);
        $model->delete();

        return $this->goBack();
    }

    /**
     * Finds the feedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id model id
     * @return feedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = feedback::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }
}
