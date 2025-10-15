<?php

namespace app\module\admin\controllers;


use app\module\admin\models\Subscriber;
use app\module\admin\models\SubscriberSearch;
use Yii;
use app\module\admin\models\User;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;



class SubscribersController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new SubscriberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if(Yii::$app->request->isPost){
            $post_data = Yii::$app->request->post();
            $model->load($post_data);
            $is_valid = $model->validate();

            try {
                if($is_valid){
                    if($model->save(false)) {
                        return $this->goBack();
                    } else {
                        throw new \Exception('Error in saving Subscriber model');
                    }
                } else {
                    $errors = $model->getErrors();
                    $response = [
                        'status' => 'error',
                        'errors' => $errors
                    ];
                    Yii::$app->response->statusCode = 422;
                    return $response;
                }
            } catch (\Throwable $exception) {
                $error = [
                    'url' => Yii::$app->request->absoluteUrl,
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'message' => $exception->getMessage()
                ];
                Yii::error($error, 'feedback');
                return $this->goBack();
            }
        }

        return $this->render('update', [
            'model'       => $model,
            'errors'      => []
        ]);
    }


    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id): \yii\web\Response
    {
        $this->findModel($id)->delete();
        return $this->goBack();
    }


    /**
     * @param $id
     * @return Subscriber|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?Subscriber
    {
        if (($model = Subscriber::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

}
