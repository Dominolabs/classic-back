<?php

namespace app\module\admin\module\order\controllers;

use app\module\admin\models\Language;
use app\module\admin\module\order\models\CityDescription;
use app\module\admin\module\order\models\City;
use app\module\admin\module\order\models\CitySearch;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class CityController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all City models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Url::remember();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new City model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new City();
        $descriptions = [];
        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = new CityDescription();

            if ($language['language_id'] === Yii::$app->params['languageId']) {
                $description->scenario = 'language-is-system';
            }

            $descriptions[$language['language_id']] = $description;
        }

        if ($model->load(Yii::$app->request->post()) && CityDescription::loadMultiple($descriptions,
                Yii::$app->request->post())) {
            $isValid = $model->validate() && CityDescription::validateMultiple($descriptions,
                    Yii::$app->request->post());

            if ($isValid && $model->save(false)) {
                // Save descriptions
                foreach ($descriptions as $key => $description) {
                    $description->city_id = $model->id;
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
     * Updates an existing City model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $languages = Language::getAll(Language::STATUS_ACTIVE);
        $descriptions = [];

        foreach ($languages as $language) {
            $description = CityDescription::findOne([
                'city_id' => $model->id,
                'language_id' => $language['language_id']
            ]);

            $descriptions[$language['language_id']] = !empty($description) ? $description : new CityDescription();

            if ($language['language_id'] === Yii::$app->params['languageId']) {
                $descriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }

        if ($model->load(Yii::$app->request->post()) && CityDescription::loadMultiple($descriptions,
                Yii::$app->request->post())) {
            $isValid = $model->validate() && CityDescription::validateMultiple($descriptions, Yii::$app->request->post());

            if ($isValid && $model->save(false)) {
                // Update descriptions
                foreach ($descriptions as $key => $description) {
                    $description->city_id = $model->id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                return $this->goBack();
            }
        }

        return $this->render('update', [
            'model' => $model,
            'descriptions' => $descriptions,
            'languages' => $languages,
        ]);
    }

    /**
     * Deletes an existing City model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        /** @var City $model */
        $model = $this->findModel($id);
        CityDescription::removeByPageId($id);
        $model->delete();

        return $this->goBack();
    }

    /**
     * Finds the City model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return City the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = City::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
