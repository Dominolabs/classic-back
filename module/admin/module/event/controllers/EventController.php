<?php

namespace app\module\admin\module\event\controllers;

use app\module\admin\module\event\models\Tag;
use Yii;
use app\module\admin\module\event\models\EventDescription;
use app\components\ImageBehavior;
use app\module\admin\models\Language;
use app\module\admin\models\User;
use app\module\admin\module\event\models\Event;
use app\module\admin\module\event\models\EventSearch;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class EventController extends Controller
{
    /**
     * @return array
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
                'class' => VerbFilter::className(),
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
        $searchModel = new EventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * @return string|\yii\web\Response
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new Event();
        $languages = Language::getAll(Language::STATUS_ACTIVE);

        //Make objects of eventDescription class for each language
        $descriptions = $this->formDescription($languages, $model);

        if(Yii::$app->request->isPost){
            $post_data = Yii::$app->request->post();
            $result = $this->validateAndStoreModels($model, $post_data, $descriptions);
            if($result){
                return $this->goBack();
            }
            $errors = $model->getErrors();
        }

        if (empty($model->sort_order)) {
            $model->sort_order = 1;
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('create', [
            'model'        => $model,
            'languages'    => $languages,
            'descriptions' => $descriptions,
            'placeholder'  => $placeholder,
            'errors'       => $errors ?? []
        ]);
    }


    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $languages = Language::getAll(Language::STATUS_ACTIVE);
        //Make objects of eventDescription class for each language
        $descriptions = $this->formDescription($languages, $model, 'update');

        if(Yii::$app->request->isPost) {
            $post_data = Yii::$app->request->post();
            $result = $this->validateAndStoreModels($model, $post_data, $descriptions, 'update');
            if($result){
                return $this->goBack();
            }
            $errors = $model->getErrors();
        }

        if (empty($model->sort_order)) {
            $model->sort_order = 1;
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('update', [
            'model'        => $model,
            'languages'    => $languages,
            'descriptions' => $descriptions,
            'placeholder'  => $placeholder,
            'errors'       => $errors ?? []
        ]);
    }


    /**
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $transaction = Event::getDb()->beginTransaction();
        try {
            $model = $this->findModel($id);
            //Remove image data
            $model->removeImage($model->image);
            //Remove tags
            $model->unlinkAll('tags', true);
            //Remove description
            EventDescription::removeByEventId($id);
            //Remove event
            $model->delete();
            $transaction->commit();

            return $this->goBack();
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            return $this->goBack();
        }

    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Event::find()
            ->where(['event_id' => $id])
            ->with(['tags' => function($q){
                $q->with('tagDescription');
            }])->one();
        if (!empty($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }


    /**
     * @param $post_data
     * @return Tag[]
     * @throws Exception
     */
    protected function fetchTags(&$post_data): ?array
    {
        try {
            $tags = json_decode($post_data['Event']['tags'], true);
            unset($post_data['Event']['tags']);
            $tag_ids = array_values(array_column($tags, 'tag_id'));
            return Tag::findAll($tag_ids);
        } catch (\Throwable $exception) {
            throw new Exception('Problem while arranging event tags' . $exception->getMessage());
        }
    }


    /**
     * @param $languages
     * @return array
     */
    private function formDescription($languages, $model, $type = 'create'): array
    {
        $descriptions = [];

        if (is_array($languages) && !empty($languages)) {
            foreach ($languages as $language) {
                if ($type === 'create') {
                    $description = new EventDescription();
                } else {
                    $description = EventDescription::findOne([
                        'event_id' => $model->event_id,
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
     * @param string $type
     * @return bool
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    private function validateAndStoreModels(&$model, $post_data, $descriptions, $type = 'created'): bool
    {
        $tags = $this->fetchTags($post_data);

        if ($model->load($post_data) && EventDescription::loadMultiple($descriptions, $post_data)) {
            $isValid = $model->validate();

            if($type === 'create'){
                $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
                if ($model->imageFile !== null) {
                    $model->image = $model->uploadImage();
                }
            } else {
                $newImageFile = UploadedFile::getInstance($model, 'imageFile');
                if (!empty($newImageFile)) {
                    $model->removeImage($model->image);
                    $model->imageFile = $newImageFile;
                    $isValid = $model->validate();
                    $model->image = $model->uploadImage();
                } else {
                    $isValid = $model->validate();
                }
            }

            $isValid = $model->validate('image') && $isValid;
            $isValid = EventDescription::validateMultiple($descriptions, $post_data) && $isValid;

            if ($isValid && $model->save(false)) {
                // Save descriptions
                foreach ($descriptions as $key => $description) {
                    $description->event_id = $model->event_id;
                    $description->date = Yii::$app->formatter->asDate($description->date, 'php:d.m.Y');
                    $description->language_id = $key;
                    $description->save(false);
                }

                //Link tags
                $model->linkAll('tags', $tags, [], true, true);
                return true;
            }
            return false;
        }
        return false;
    }
}
