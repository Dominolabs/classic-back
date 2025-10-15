<?php

namespace app\module\admin\module\event\controllers;

use app\components\ImageBehavior;
use app\module\admin\models\VacancyDescription;
use app\module\admin\models\VacancySearch;
use app\module\admin\module\event\models\Tag;
use app\module\admin\module\event\models\TagDescription;
use app\module\admin\module\event\models\TagSearch;
use app\module\admin\module\pizzeria\models\Pizzeria;
use app\module\admin\module\pizzeria\models\PizzeriaDescription;
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

class TagController extends Controller
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
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new Tag model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed create view or response object
     */
    public function actionCreate()
    {
        $dataProviders = [];
        $model = new Tag();
        $languages = Language::getAll(Language::STATUS_ACTIVE);

        //Make objects of tagDescription class for each language
        $descriptions = $this->formDescription($languages, $model);

        $post_data = Yii::$app->request->post();

        $result = $this->validateAndStoreModels($model, $post_data, $descriptions);

        if ($result) {
            return $this->goBack();
        }
        $errors = $model->getErrors();

        return $this->render('create', [
            'model' => $model,
            'languages' => $languages,
            'descriptions' => $descriptions,
            'dataProviders' => $dataProviders,
            'errors' => $errors
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
        $languages = Language::getAll(Language::STATUS_ACTIVE);

        //Make objects of vacancyDescription class for each language
        $descriptions = $this->formDescription($languages, $model, 'edit');

        $post_data = Yii::$app->request->post();

        $result = $this->validateAndStoreModels($model, $post_data, $descriptions);

        if ($result) {
            return $this->goBack();
        }
        $errors = $model->getErrors();

        return $this->render('update', [
            'model' => $model,
            'languages' => $languages,
            'descriptions' => $descriptions,
            'errors' => $errors
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
     * @return Tag|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?Tag
    {
        if (($model = Tag::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }


    /**
     * @param $languages
     * @param $model
     * @param string $type
     * @return array
     */
    private function formDescription($languages, $model, $type = 'create'): array
    {
        $descriptions = [];

        if (is_array($languages) && !empty($languages)) {
            foreach ($languages as $language) {
                if ($type === 'create') {
                    $description = new TagDescription();
                } else {
                    $description = TagDescription::findOne([
                        'tag_id' => $model->tag_id,
                        'language_id' => $language['language_id']
                    ]);
                }
                if ((int)$language['language_id'] === (int)Yii::$app->params['languageId']) {
                    $description->scenario = 'language-is-system';
                }
                $descriptions[$language['language_id']] = $description;
            }
        }

        return $descriptions;
    }


    /**
     * @param $model
     * @param $post_data
     * @param $descriptions
     * @return bool
     */
    private function validateAndStoreModels(&$model, $post_data, $descriptions): bool
    {
        if (TagDescription::loadMultiple($descriptions, $post_data)) {

            $isValid = TagDescription::validateMultiple($descriptions, $post_data);

            if ($isValid && $model->save(false)) {
                foreach ($descriptions as $key => $description) {
                    $description->tag_id = $model->tag_id;
                    $description->language_id = $key;
                    $description->save(false);
                }
                return true;
            }
            return false;
        }
        return false;
    }
}
