<?php
/**
 * LanguageController class file.
 */

namespace app\module\admin\controllers;

use Yii;
use app\module\admin\models\User;
use app\components\ImageBehavior;
use app\module\admin\models\Language;
use app\module\admin\models\LanguageSearch;
use app\module\admin\models\BannerImage;
use app\module\admin\models\Message;
use app\module\admin\models\PageDescription;
use app\module\admin\models\SeoUrl;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;

/**
 * LanguageController implements the CRUD actions for Language model.
 *
 * @package app\module\admin\controllers
 */
class LanguageController extends Controller
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Language models.
     *
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new LanguageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Language model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed create view or response object
     */
    public function actionCreate()
    {
        /* @var Language|ImageBehavior $model */
        $model = new Language();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if ($model->validate()) {
                $model->image = $model->uploadImage();

                if ($model->save(false)) {
                    Yii::$app->cache->flush();

                    return $this->goBack();
                }
            }
        }

        if (empty($model->sort_order)) {
            $model->sort_order = 1;
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Language model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Language id
     * @return mixed update view or response object
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        /* @var Language|ImageBehavior $model */
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $newImageFile = UploadedFile::getInstance($model, 'imageFile');

            if (!empty($newImageFile)) {
                $model->removeImage($model->image); // Remove old image
                $model->imageFile = $newImageFile;

                if ($isValid = $model->validate()) {
                    $model->image = $model->uploadImage();
                }
            } else {
                $isValid = $model->validate();
            }

            if ($isValid && $model->save(false)) {
                Yii::$app->cache->flush();

                return $this->goBack();
            }
        }

        if (empty($model->sort_order)) {
            $model->sort_order = 1;
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Language model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * Last language can't be deleted. If main language was deleted, another language will be set as default language.
     *
     * @param integer $id Language id
     * @return mixed response object
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception|\Throwable in case delete failed.
     * @throws \yii\db\StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     */
    public function actionDelete($id)
    {
        if (Language::getAllCount() > 1) {
            /* @var Language|ImageBehavior $model */
            $model = $this->findModel($id);

            // If main language was deleted, another language will be set as default language
            if (Yii::$app->params['languageId'] == $id) {
                $languages = Language::getAll();

                foreach ($languages as $language) {
                    if ($language['language_id'] != $id) {
                        Language::setMainLanguage($language['language_id']);
                        break;
                    }
                }
            }

            // Remove related data
            /** @var BannerImage|ImageBehavior $banner */
            foreach (BannerImage::find()->where(['language_id' => $id])->all() as $banner) {
                    $banner->removeImage($banner->image);
                $banner->delete();
            }

            Message::deleteAll(['language_id' => $id]);
            PageDescription::deleteAll(['language_id' => $id]);
            SeoUrl::deleteAll(['language_id' => $id]);

            // Remove model data
            $model->removeImage($model->image);
            $model->delete();

            Yii::$app->cache->flush();
        }

        return $this->goBack();
    }

    /**
     * Finds the Language model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Language id
     * @return Language the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Language::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }
}
