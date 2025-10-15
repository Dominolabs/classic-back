<?php

namespace app\module\admin\module\pizzeria\controllers;

use Yii;
use app\module\admin\module\pizzeria\models\PizzeriaDescription;
use app\components\ImageBehavior;
use app\module\admin\models\Language;
use app\module\admin\models\User;
use app\module\admin\module\pizzeria\models\Pizzeria;
use app\module\admin\module\pizzeria\models\PizzeriaSearch;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * PizzeriaController implements the CRUD actions for Pizzeria model.
 */
class PizzeriaController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all Pizzeria models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PizzeriaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Pizzeria model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * 
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var Pizzeria|ImageBehavior $model */
        $model = new Pizzeria();
        $languages = Language::getAll(Language::STATUS_ACTIVE);
        $descriptions = [];

        foreach ($languages as $language) {
            $description = new PizzeriaDescription();

            if ((int) $language['language_id'] === (int) Yii::$app->params['languageId']) {
                $description->scenario = 'language-is-system';
            }

            $descriptions[$language['language_id']] = $description;
        }

        if ($model->load(Yii::$app->request->post()) && PizzeriaDescription::loadMultiple($descriptions, Yii::$app->request->post())) {
            $isValid = $model->validate();
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->imageFile !== null) {
                $model->image = $model->uploadImage();
            }
            $isValid = $model->validate('image') && $isValid;
            $isValid = PizzeriaDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;

            if ($isValid && $model->save(false)) {
                // Save descriptions
                foreach ($descriptions as $key => $description) {
                    $description->pizzeria_id = $model->pizzeria_id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                return $this->goBack();
            }
        }

        if (empty($model->sort_order)) {
            $model->sort_order = 1;
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('create', [
            'model' => $model,
            'languages' => $languages,
            'descriptions' => $descriptions,
            'placeholder' => $placeholder
        ]);
    }

    /**
     * Updates an existing Pizzeria model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @throws NotFoundHttpException
     * @throws \Exception|\Throwable
     * @return mixed
     */
    public function actionUpdate($id)
    {
        /** @var Pizzeria|ImageBehavior $model */
        $model = $this->findModel($id);
        $languages = Language::getAll(Language::STATUS_ACTIVE);
        $descriptions = [];

        foreach ($languages as $language) {
            $description = PizzeriaDescription::findOne([
                'pizzeria_id' => $model->pizzeria_id,
                'language_id' => $language['language_id']
            ]);

            $descriptions[$language['language_id']] = $description ?: new PizzeriaDescription();

            if ((int) $language['language_id'] === (int) Yii::$app->params['languageId']) {
                $descriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }

        if ($model->load(Yii::$app->request->post()) && PizzeriaDescription::loadMultiple($descriptions, Yii::$app->request->post())) {
            $newImageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($newImageFile !== null) {
                $model->removeImage($model->image); // Remove old image
                $model->imageFile = $newImageFile;
                $isValid = $model->validate();
                $model->image = $model->uploadImage();
            } else {
                $isValid = $model->validate();
            }
            $isValid = PizzeriaDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;

            if ($isValid && $model->save(false)) {
                // Update descriptions
                foreach ($descriptions as $key => $description) {
                    $description->pizzeria_id = $model->pizzeria_id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                return $this->goBack();
            }
        }

        if (empty($model->sort_order)) {
            $model->sort_order = 1;
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('update', [
            'model' => $model,
            'languages' => $languages,
            'descriptions' => $descriptions,
            'placeholder' => $placeholder
        ]);
    }

    /**
     * Deletes an existing Pizzeria model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception|\Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        /** @var Pizzeria|ImageBehavior $model */
        $model =  $this->findModel($id);

        $this->findModel($id)->delete();
        $model->removeImage($model->image);
        PizzeriaDescription::removeByPizzeriaId($id);
        $model->delete();

        return $this->goBack();
    }

    /**
     * Finds the Pizzeria model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Pizzeria
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Pizzeria::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
