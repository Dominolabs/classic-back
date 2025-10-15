<?php

namespace app\module\admin\module\event\controllers;

use Yii;
use app\module\admin\models\Language;
use app\module\admin\models\User;
use app\module\admin\module\event\models\EventCategoryDescription;
use app\module\admin\module\event\models\EventCategory;
use app\module\admin\module\event\models\EventCategorySearch;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class EventCategoryController extends Controller
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
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all EventCategory models.
     *
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new EventCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new EventCategory model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed create view
     */
    public function actionCreate()
    {
        $model = new EventCategory();
        $descriptions = [];
        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = new EventCategoryDescription();

            if ($language['language_id'] == Yii::$app->params['languageId']) {
                $description->scenario = 'language-is-system';
            }

            $descriptions[$language['language_id']] = $description;
        }

        if ($model->load(Yii::$app->request->post()) && EventCategoryDescription::loadMultiple($descriptions, Yii::$app->request->post())) {
            $isValid = $model->validate();
            $isValid = EventCategoryDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;

            if ($isValid && $model->save(false)) {
                // Save descriptions
                foreach ($descriptions as $key => $description) {
                    $description->event_category_id = $model->event_category_id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                return $this->goBack();
            }
        }

        if (empty($model->sort_order)) {
            $model->sort_order = 1;
        }

        return $this->render('create', [
            'model' => $model,
            'descriptions' => $descriptions,
            'languages' => $languages,
        ]);
    }

    /**
     * Updates an existing EventCategory model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id model id
     * @throws NotFoundHttpException if model not found
     * @return mixed update view
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $descriptions = [];
        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = EventCategoryDescription::findOne([
                'event_category_id' => $model->event_category_id,
                'language_id' => $language['language_id']
            ]);

            $descriptions[$language['language_id']] = (!empty($description)) ? $description : new EventCategoryDescription();

            if ($language['language_id'] == Yii::$app->params['languageId']) {
                $descriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }

        if ($model->load(Yii::$app->request->post()) && EventCategoryDescription::loadMultiple($descriptions, Yii::$app->request->post())) {
            $isValid = $model->validate();
            $isValid = EventCategoryDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;

            if ($isValid && $model->save(false)) {
                // Update descriptions
                foreach ($descriptions as $key => $description) {
                    $description->event_category_id = $model->event_category_id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                return $this->goBack();
            }
        }

        if (empty($model->sort_order)) {
            $model->sort_order = 1;
        }

        return $this->render('update', [
            'model' => $model,
            'descriptions' => $descriptions,
            'languages' => $languages,
        ]);
    }

    /**
     * Deletes an existing EventCategory model.
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
        $this->findModel($id)->delete();
        EventCategoryDescription::removeByEventCategoryId($id);

        return $this->goBack();
    }

    /**
     * Finds the EventCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id model id
     * @return EventCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }
}
