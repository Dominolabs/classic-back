<?php
/**
 * ModuleController class file.
 */

namespace app\module\admin\controllers;

use Yii;
use app\module\admin\models\User;
use app\module\admin\models\ModuleUploadForm;
use app\module\admin\models\Module;
use app\module\admin\models\ModuleSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use kartik\grid\EditableColumnAction;

/**
 * ModuleController implements the CRUD actions for Module model.
 */
class ModuleController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'editSortOrder' => [
                'class' => EditableColumnAction::class,
                'modelClass' => Module::class
            ]
        ]);
    }

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
     * Lists all Module models.
     *
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new ModuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new ModuleUploadForm();

        if (Yii::$app->request->isAjax) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->upload()) {
                Yii::$app->session->setFlash('success', 'Модуль успешно установлен.');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось установить модуль.');
            }

            return $this->goBack();
        }

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Module model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Module id
     * @return mixed response object
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception|\Throwable in case delete failed
     * @throws \yii\db\StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->removeModule()) {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Модуль успешно удален.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить модуль.');
        }

        return $this->goBack();
    }

    /**
     * Activates module by setting module status to 'Active'.
     *
     * @param int $id Module id
     * @return mixed response object
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);

        $model->status = Module::STATUS_ACTIVE;
        $model->save(false);

        return $this->goBack();
    }

    /**
     * Deactivates module by setting module status to 'Not active'.
     *
     * @param int $id Module id
     * @return mixed response object
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeactivate($id)
    {
        $model = $this->findModel($id);

        $model->status = Module::STATUS_NOT_ACTIVE;
        $model->save(false);

        return $this->goBack();
    }

    /**
     * Finds the Module model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id model id
     * @return Module the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Module::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }
}
