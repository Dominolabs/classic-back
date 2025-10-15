<?php

namespace app\module\admin\controllers;

use Exception;
use Throwable;
use Yii;
use app\components\ImageBehavior;
use app\module\admin\models\User;
use app\module\admin\models\Language;
use app\module\admin\models\RestaurantDescription;
use app\module\admin\models\SeoUrl;
use app\module\admin\models\Restaurant;
use app\module\admin\models\RestaurantSearch;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class RestaurantController extends Controller
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
        $searchModel = new RestaurantSearch();
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
        /** @var Restaurant|ImageBehavior $restaurant */
        $restaurant = new Restaurant();

        $descriptions = [];
        $seoUrls = [];

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = new RestaurantDescription();
            $seoUrl = new SeoUrl();

            if ((int)$language['language_id'] === (int)Language::getLanguageIdByCode(Yii::$app->language)) {
                $description->scenario = 'language-is-system';
            }

            $descriptions[$language['language_id']] = $description;
            $seoUrls[$language['language_id']] = $seoUrl;
        }

        if ($restaurant->load(Yii::$app->request->post())
            && RestaurantDescription::loadMultiple($descriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {

            $restaurant->imageFile = UploadedFile::getInstance($restaurant, 'imageFile');
            $restaurant->backgroundImageFile = UploadedFile::getInstance($restaurant, 'backgroundImageFile');
            $restaurant->imageTransparentFile = UploadedFile::getInstance($restaurant, 'imageTransparentFile');

            $isValid = $restaurant->validate();

            if ($restaurant->imageFile !== null) {
                $restaurant->image = $restaurant->uploadImage('imageFile');
            }
            if ($restaurant->backgroundImageFile !== null) {
                $restaurant->background_image = $restaurant->uploadImage('backgroundImageFile');
            }
            if ($restaurant->imageTransparentFile !== null) {
                $restaurant->background_image = $restaurant->uploadImage('imageTransparentFile');
            }

            $isValid = $restaurant->validate('image') && $restaurant->validate('image_transparent') && $restaurant->validate('background_image') && $isValid;
            $isValid = RestaurantDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;

            if ($isValid && $restaurant->save(false)) {
                foreach ($descriptions as $key => $description) {
                    $description->restaurant_id = $restaurant->restaurant_id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                // Save SEO URLs
                $restaurantName = $descriptions[Language::getLanguageIdByCode(Yii::$app->language)]->title;

                /**
                 * @var int $key language id
                 * @var SeoUrl  $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'restaurant_id=' . $restaurant->restaurant_id;
                    $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($restaurantName), $key);

                    $seoUrl->save(false);
                }

                return $this->goBack();
            }
        }

        if (empty($restaurant->sort_order)) {
            $restaurant->sort_order = 1;
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('create', [
            'restaurant' => $restaurant,
            'descriptions' => $descriptions,
            'seoUrls' => $seoUrls,
            'languages' => $languages,
            'placeholder' => $placeholder
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /** @var Restaurant|ImageBehavior $restaurant */
        $restaurant = $this->findModel($id);

        $descriptions = [];
        $seoUrls = [];

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = RestaurantDescription::findOne([
                'restaurant_id' => $restaurant->restaurant_id,
                'language_id' => $language['language_id']
            ]);

            $seoUrl = SeoUrl::findOne([
                'query' => 'restaurant_id=' . $restaurant->restaurant_id,
                'language_id' => $language['language_id']
            ]);

            $descriptions[$language['language_id']] = (!empty($description)) ? $description : new RestaurantDescription();
            $seoUrls[$language['language_id']] = (!empty($seoUrl)) ? $seoUrl : new SeoUrl();

            if ((int)$language['language_id'] === (int)Language::getLanguageIdByCode(Yii::$app->language)) {
                $descriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }

        if ($restaurant->load(Yii::$app->request->post())
            && RestaurantDescription::loadMultiple($descriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {

            $newImageFile = UploadedFile::getInstance($restaurant, 'imageFile');
            $newImageTransparentFile = UploadedFile::getInstance($restaurant, 'imageTransparentFile');
            $newBackgroundImageFile = UploadedFile::getInstance($restaurant, 'backgroundImageFile');

            if ($newImageFile !== null) {
                $restaurant->removeImage($restaurant->image); // Remove old image
                $restaurant->imageFile = $newImageFile;
                $isValid = $restaurant->validate();
                $restaurant->image = $restaurant->uploadImage('imageFile');
            }

            if ($newImageTransparentFile !== null) {
                $restaurant->removeImage($restaurant->image_transparent); // Remove old image
                $restaurant->imageTransparentFile = $newImageTransparentFile;
                $isValid = null ? $restaurant->validate() : true;
                $restaurant->image_transparent = $restaurant->uploadImage('imageTransparentFile');
            }

            if ($newBackgroundImageFile !== null) {
                $restaurant->removeImage($restaurant->background_image); // Remove old image
                $restaurant->backgroundImageFile = $newBackgroundImageFile;
                $isValid = null ? $restaurant->validate() : true;
                $restaurant->background_image = $restaurant->uploadImage('backgroundImageFile');
            }

            $isValid = $isValid ?? $restaurant->validate();

            $isValid = RestaurantDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;

            if ($isValid && $restaurant->save(false)) {
                foreach ($descriptions as $key => $description) {
                    $description->restaurant_id = $restaurant->restaurant_id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                // Update SEO URLs
                $restaurantName = $descriptions[Language::getLanguageIdByCode(Yii::$app->language)]->title;

                /**
                 * @var int $key language id
                 * @var SeoUrl  $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'restaurant_id=' . $restaurant->restaurant_id;

                    if (empty($seoUrl->keyword)) {
                        $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($restaurantName), $key);
                    }

                    $seoUrl->save(false);
                }

                return $this->goBack();
            }
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('update', [
            'restaurant' => $restaurant,
            'descriptions' => $descriptions,
            'seoUrls' => $seoUrls,
            'languages' => $languages,
            'placeholder' => $placeholder
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
        /** @var Restaurant|ImageBehavior $model */
        $model = $this->findModel($id);
        $model->removeImage($model->image);
        $model->removeImage($model->image_transparent);
        $model->removeImage($model->background_image);

        SeoUrl::removeByQuery('restaurant_id=' . $id);
        RestaurantDescription::removeByRestaurantId($id);
        $model->delete();

        return $this->goBack();
    }

    /**
     * @param integer $id
     * @return Restaurant
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Restaurant
    {
        if (($model = Restaurant::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
