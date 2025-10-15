<?php

namespace app\module\admin\controllers;

use app\components\ImageBehavior;
use app\module\admin\models\VacancyDescription;
use app\module\admin\models\VacancyRequest;
use app\module\admin\models\VacancyRequestForm;
use app\module\admin\models\VacancyRequestSearch;
use app\module\admin\models\VacancySearch;
use app\module\admin\module\pizzeria\models\Pizzeria;
use app\module\admin\module\pizzeria\models\PizzeriaDescription;
use Behat\Gherkin\Exception\CacheException;
use Yii;
use app\module\admin\models\User;
use app\module\admin\models\Vacancy;
use app\module\admin\models\Language;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;

class VacancyRequestsController extends Controller
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
     * Lists all Vacancy models.
     *
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new VacancyRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $placeholder = ImageBehavior::placeholder(100, 100);

        Url::remember();
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'placeholder'  => $placeholder
        ]);
    }


    /**
     * Updates an existing Vacancy model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Banner id
     * @return mixed update view or response object
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception|\Throwable in case delete failed
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
                        throw new \Exception('Error in saving Vacancy Request model');
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

        $placeholder = ImageBehavior::placeholder(100, 100);
        return $this->render('update', [
            'model'       => $model,
            'placeholder' => $placeholder,
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
     * @return VacancyRequest|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?VacancyRequest
    {
        if (($model = VacancyRequest::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

}
