<?php

namespace app\module\admin\module\gallery\controllers;

use mikemadisonweb\rabbitmq\components\Producer;
use app\module\admin\models\SeoUrl;
use app\module\admin\module\gallery\models\AlbumDescription;
use app\components\ImageBehavior;
use app\module\admin\models\Language;
use app\module\admin\module\gallery\models\AlbumImage;
use app\module\admin\models\User;
use app\module\admin\module\gallery\models\Album;
use app\module\admin\module\gallery\models\AlbumSearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class AlbumController extends Controller
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
     * Lists all Album models.
     *
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new AlbumSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Album model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed create view
     */
    public function actionCreate()
    {
        /** @var Album|ImageBehavior $model */
        $model = new Album();
        $languages = Language::getAll(Language::STATUS_ACTIVE);
        $errors = [];
        $descriptions = [];
        $seoUrls = [];
        foreach ($languages as $language) {
            $description = new AlbumDescription();
            $seoUrl = new SeoUrl();
            if ($language['language_id'] === Yii::$app->params['languageId']) {
                $description->scenario = 'language-is-system';
            }
            $descriptions[$language['language_id']] = $description;
            $seoUrls[$language['language_id']] = $seoUrl;
        }
        $albumImagesDataProvider = AlbumImage::getDataProvider($model->album_id);
        if ($model->load(Yii::$app->request->post()) && AlbumDescription::loadMultiple($descriptions, Yii::$app->request->post()) &&
            SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {
            $isValid = $model->validate();
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->imageFile !== null) {
                $model->image = $model->uploadImage();
            }
            $isValid = $model->validate('image') && $isValid;
            $isValid = AlbumDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;
            if ($isValid && $model->save(false)) {
                // Save descriptions
                foreach ($descriptions as $key => $description) {
                    $description->album_id = $model->album_id;
                    $description->language_id = $key;
                    $description->save(false);
                }
                // Update album images
                $imagesJson = Json::decode($model->images);
                if (!empty($imagesJson)) {
                    foreach ($imagesJson as $key => $image) {
                        if (!empty($image)) {
                            $albumImage = new AlbumImage();
                            $albumImage->album_id = $model->album_id;
                            $albumImage->sort_order = $key;
                            $albumImage->image = $image;
                            $albumImage->save(false);
                            $albumImage->moveUploadedFile();
                        }
                    }
                }
                // Save SEO URLs
                $albumName = $descriptions[Language::getLanguageIdByCode(Yii::$app->language)]->name;
                /**
                 * @var int $key language id
                 * @var SeoUrl  $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'album_id=' . $model->album_id;
                    $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($albumName), $key);
                    $seoUrl->save(false);
                }

                return $this->goBack();
            }
        }
        if (empty($model->sort_order)) {
            $model->sort_order = 1;
        }
        $model->setImages($albumImagesDataProvider);
        $placeholder = ImageBehavior::placeholder(100, 100);
        return $this->render('create', [
            'model' => $model,
            'languages' => $languages,
            'descriptions' => $descriptions,
            'albumImagesDataProvider' => $albumImagesDataProvider,
            'initialPreview' => AlbumImage::getInitialPreview($albumImagesDataProvider),
            'initialPreviewConfig' => AlbumImage::getInitialPreviewConfig($albumImagesDataProvider),
            'placeholder' => $placeholder,
            'errors' => $errors,
            'seoUrls' => $seoUrls,
        ]);
    }

    /**
     * Updates an existing Album model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id model id
     * @throws NotFoundHttpException if model not found
     * @throws \Exception|\Throwable in case delete failed
     * @throws \yii\db\StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     * @return mixed update view
     */
    public function actionUpdate($id)
    {
        /** @var Album|ImageBehavior $model */
        $model = $this->findModel($id);
        $languages = Language::getAll(Language::STATUS_ACTIVE);
        $errors = [];
        $descriptions = [];
        $seoUrls = [];
        foreach ($languages as $language) {
            $description = AlbumDescription::findOne([
                'album_id' => $model->album_id,
                'language_id' => $language['language_id']
            ]);
            $seoUrl = SeoUrl::findOne([
                'query' => 'album_id=' . $model->album_id,
                'language_id' => $language['language_id']
            ]);
            $descriptions[$language['language_id']] = (!empty($description)) ? $description : new AlbumDescription();
            $seoUrls[$language['language_id']] = (!empty($seoUrl)) ? $seoUrl : new SeoUrl();

            if ($language['language_id'] === Yii::$app->params['languageId']) {
                $descriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }
        $albumImagesDataProvider = AlbumImage::getDataProvider($model->album_id);
        if ($model->load(Yii::$app->request->post()) && AlbumDescription::loadMultiple($descriptions, Yii::$app->request->post()) &&
            SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {
            $newImageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($newImageFile !== null) {
                $model->removeImage($model->image); // Remove old image
                $model->imageFile = $newImageFile;
                $isValid = $model->validate();
                $model->image = $model->uploadImage();
            } else {
                $isValid = $model->validate();
            }
            $isValid = AlbumDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;
            if ($isValid && $model->save(false)) {
                // Update descriptions
                foreach ($descriptions as $key => $description) {
                    $description->album_id = $model->album_id;
                    $description->language_id = $key;
                    $description->save(false);
                }
                // Update album images
                AlbumImage::removeByAlbumId($id);
                $imagesJson = Json::decode($model->images);
                if (!empty($imagesJson)) {
                    foreach ($imagesJson as $key => $image) {
                        if (!empty($image)) {
                            $albumImage = new AlbumImage();
                            $albumImage->album_id = $model->album_id;
                            $albumImage->sort_order = $key;
                            $albumImage->image = $image;
                            $albumImage->save(false);
                            $albumImage->moveUploadedFile();
                        }
                    }
                }
                // Update SEO URLs
                $albumName = $descriptions[Language::getLanguageIdByCode(Yii::$app->language)]->name;
                /**
                 * @var int $key language id
                 * @var SeoUrl  $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'album_id=' . $model->album_id;
                    if (empty($seoUrl->keyword)) {
                        $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($albumName), $key);
                    }
                    $seoUrl->save(false);
                }

                return $this->goBack();
            }
        }
        if (empty($model->sort_order)) {
            $model->sort_order = 1;
        }
        $model->setImages($albumImagesDataProvider);
        $placeholder = ImageBehavior::placeholder(100, 100);
        return $this->render('update', [
            'model' => $model,
            'languages' => $languages,
            'descriptions' => $descriptions,
            'albumImagesDataProvider' => $albumImagesDataProvider,
            'initialPreview' => AlbumImage::getInitialPreview($albumImagesDataProvider),
            'initialPreviewConfig' => AlbumImage::getInitialPreviewConfig($albumImagesDataProvider),
            'placeholder' => $placeholder,
            'errors' => $errors,
            'seoUrls' => $seoUrls
        ]);
    }

    /**
     * Deletes an existing Album model.
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
        /** @var Album|ImageBehavior $model */
        $model =  $this->findModel($id);
        $this->findModel($id)->delete();
        $model->removeImage($model->image);
        AlbumDescription::removeByAlbumId($id);
        AlbumImage::removeByAlbumId($id, true);
        SeoUrl::removeByQuery('album_id=' . $id);
        $model->delete();
        return $this->goBack();
    }

    /**
     * Upload images.
     * @return string JSON encoded list of uploaded images
     * @throws \yii\base\Exception if failed to save image
     */
    public function actionUploadImage()
    {
        $files = [];
        $model = new AlbumImage();
        $model->imageFile = UploadedFile::getInstances($model, 'imageFile');
        $directory = Yii::getAlias('@app/web/image/temp') . DIRECTORY_SEPARATOR;
        if (!is_dir($directory)) {
            FileHelper::createDirectory($directory);
        }
        foreach ($model->imageFile as $key => $file) {
            $fileName = ImageBehavior::generateImageName($file->extension);
            $filePath = $directory . $fileName;
            if ($file->saveAs($filePath)) {
                $files[$key] = $fileName;
            }
        }
        return $this->asJson($files);
    }

    /**
     * Removes album image.
     * @return string removed image name
     */
    public function actionDeleteImage()
    {
        return $this->asJson(Yii::$app->request->post('key'));
    }

    /**
     * Finds the Album model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id model id
     * @return Album the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Album::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
