<?php

namespace app\module\admin\controllers;

use Exception;
use Throwable;
use Yii;
use app\module\admin\models\User;
use app\components\ImageBehavior;
use app\module\admin\models\BannerImage;
use app\module\admin\models\Banner;
use app\module\admin\models\BannerSearch;
use app\module\admin\models\Language;
use yii\data\ArrayDataProvider;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;

class BannerController extends Controller
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
                        'matchCallback' => static function ($rule, $action) {
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
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BannerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var Banner|ImageBehavior $model */
        $model = new Banner();

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        $dataProviders = [];
        $errors = [];

        foreach ($languages as $language) {
            $bannerImages = [];

            if (isset(Yii::$app->request->post('BannerImage')[$language['language_id']])) {
                $postData = [];
                $bannerImagesPost = Yii::$app->request->post('BannerImage')[$language['language_id']];

                $count = count($bannerImagesPost);

                for ($i = 0; $i < $count; $i++) {
                    $bannerImages[] = new BannerImage();
                }

                foreach ($bannerImagesPost as $rawPostDataItem) {
                    $postData['BannerImage'][] = $rawPostDataItem;
                }

                BannerImage::loadMultiple($bannerImages, $postData);
            }

            $dataProvider = new ArrayDataProvider([
                'key' => 'banner_image_id',
                'modelClass' => 'app\module\admin\models\BannerImage',
                'allModels' => $bannerImages,
                'pagination' => false,
            ]);

            $dataProviders[$language['language_id']] = $dataProvider;
        }

        if ($model->load(Yii::$app->request->post())) {

            $isValid = $model->validate();

            foreach ($languages as $language) {
                $bannerImagesModels = $dataProviders[$language['language_id']]->getModels();

                $isValid = BannerImage::validateMultiple($bannerImagesModels) && $isValid;

                if (!$isValid) {
                    /** @var BannerImage|ImageBehavior $bannerImagesModel */
                    foreach ($bannerImagesModels as $bannerImagesModel) {
                        $errors[$language['language_id']]['BannerImage'][] = $bannerImagesModel->getErrors();
                    }
                }
            }

            if ($isValid && $model->save(false)) {
                foreach ($languages as $language) {
                    $bannerImagesModels = $dataProviders[$language['language_id']]->getModels();

                    foreach ($bannerImagesModels as $key => $bannerImagesModel) {
                        $bannerImagesModel->banner_id = $model->banner_id;
                        $bannerImagesModel->language_id = $language['language_id'];

                        $bannerImagesModel->imageFile = UploadedFile::getInstance($bannerImagesModel, "[{$language['language_id']}][{$key}]imageFile");

                        if ($bannerImagesModel->validate()) {
                            if (!empty($bannerImagesModel->imageFile)) {
                                $bannerImagesModel->image = $bannerImagesModel->uploadImage();
                            }

                            $bannerImagesModel->save(false);
                        }
                    }
                }

                return $this->goBack();
            }
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('create', [
            'model' => $model,
            'languages' => $languages,
            'dataProviders' => $dataProviders,
            'placeholder' => $placeholder,
            'errors' => $errors
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception|Throwable
     * @throws StaleObjectException
     */
    public function actionUpdate($id)
    {
        /** @var $model Banner|ImageBehavior */
        $model = $this->findModel($id);

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        $dataProviders = [];
        $errors = [];

        foreach ($languages as $language) {
            $bannerImages = [];

            if (isset(Yii::$app->request->post('BannerImage')[$language['language_id']])) {
                $postData = [];
                $bannerImagesPost = Yii::$app->request->post('BannerImage')[$language['language_id']];

                $count = count($bannerImagesPost);

                for ($i = 0; $i < $count; $i++) {
                    $bannerImages[] = new BannerImage();
                }

                foreach ($bannerImagesPost as $rawPostDataItem) {
                    $postData['BannerImage'][] = $rawPostDataItem;
                }

                BannerImage::loadMultiple($bannerImages, $postData);
            } else {
                $bannerImages = BannerImage::getAllByBannerIdAndLanguageId($model->banner_id, $language['language_id']);
            }

            $dataProvider = new ArrayDataProvider([
                'key' => 'banner_image_id',
                'modelClass' => BannerImage::class,
                'allModels' => $bannerImages,
                'pagination' => false,
            ]);

            $dataProviders[$language['language_id']] = $dataProvider;
        }

        if ($model->load(Yii::$app->request->post())) {

            $isValid = $model->validate();

            foreach ($languages as $language) {
                $bannerImagesModels = $dataProviders[$language['language_id']]->getModels();

                $isValid = BannerImage::validateMultiple($bannerImagesModels) && $isValid;

                if (!$isValid) {
                    /** @var BannerImage|ImageBehavior $bannerImagesModel */
                    foreach ($bannerImagesModels as $bannerImagesModel) {
                        $errors[$language['language_id']]['BannerImage'][] = $bannerImagesModel->getErrors();
                    }
                }
            }

            if ($isValid && $model->save(false)) {
                BannerImage::removeByBannerId($id);

                foreach ($languages as $language) {
                    $bannerImagesModels = $dataProviders[$language['language_id']]->getModels();

                    foreach ($bannerImagesModels as $key => $bannerImagesModel) {
                        $bannerImagesModel->banner_id = $model->banner_id;
                        $bannerImagesModel->language_id = $language['language_id'];

                        $bannerImagesModel->imageFile = UploadedFile::getInstance($bannerImagesModel, "[{$language['language_id']}][{$key}]imageFile");

                        if ($bannerImagesModel->validate()) {
                            if (!empty($bannerImagesModel->imageFile)) {
                                $bannerImagesModel->image = $bannerImagesModel->uploadImage();
                            }

                            $bannerImagesModel->save(false);
                        }
                    }
                }

                return $this->goBack();
            }
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('update', [
            'model' => $model,
            'languages' => $languages,
            'dataProviders' => $dataProviders,
            'placeholder' => $placeholder,
            'errors' => $errors
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception|Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        BannerImage::removeByBannerId($id, true);

        return $this->goBack();
    }

    /**
     * @param integer $id
     * @return Banner
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Banner
    {
        if (($model = Banner::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
