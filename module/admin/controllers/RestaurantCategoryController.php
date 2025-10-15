<?php

namespace app\module\admin\controllers;

use app\module\admin\models\SeoUrl;
use Exception;
use Throwable;
use Yii;
use app\module\admin\models\Language;
use app\module\admin\models\User;
use app\module\admin\models\RestaurantCategoryDescription;
use app\module\admin\models\RestaurantCategory;
use app\module\admin\models\RestaurantCategorySearch;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class RestaurantCategoryController extends Controller
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
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new RestaurantCategorySearch();
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
        $model = new RestaurantCategory();
        $descriptions = [];
        $languages = Language::getAll(Language::STATUS_ACTIVE);
        $seoUrls = [];

        foreach ($languages as $language) {
            $description = new RestaurantCategoryDescription();
            $seoUrl = new SeoUrl();

            if ($language['language_id'] == Yii::$app->params['languageId']) {
                $description->scenario = 'language-is-system';
            }

            $descriptions[$language['language_id']] = $description;
            $seoUrls[$language['language_id']] = $seoUrl;
        }

        if ($model->load(Yii::$app->request->post())
            && RestaurantCategoryDescription::loadMultiple($descriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {

            $isValid = $model->validate();
            $isValid = RestaurantCategoryDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;

            if ($isValid && $model->save(false)) {
                // Save descriptions
                foreach ($descriptions as $key => $description) {
                    $description->restaurant_category_id = $model->restaurant_category_id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                $name = $descriptions[Language::getLanguageIdByCode(Yii::$app->language)]->name;

                /**
                 * @var int $key language id
                 * @var SeoUrl $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'restaurant_category_id=' . $model->restaurant_category_id;
                    $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($name), $key);

                    $seoUrl->save(false);
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
            'seoUrls' => $seoUrls,
        ]);
    }

    /**
     * @param integer $id
     * @throws NotFoundHttpException
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $descriptions = [];
        $languages = Language::getAll(Language::STATUS_ACTIVE);
        $seoUrls = [];

        foreach ($languages as $language) {
            $description = RestaurantCategoryDescription::findOne([
                'restaurant_category_id' => $model->restaurant_category_id,
                'language_id' => $language['language_id']
            ]);
            $seoUrl = SeoUrl::findOne([
                'query' => 'restaurant_category_id=' . $model->restaurant_category_id,
                'language_id' => $language['language_id']
            ]);

            $descriptions[$language['language_id']] = (!empty($description)) ? $description : new RestaurantCategoryDescription();

            if ($language['language_id'] == Yii::$app->params['languageId']) {
                $descriptions[$language['language_id']]->scenario = 'language-is-system';
            }
            $seoUrls[$language['language_id']] = (!empty($seoUrl)) ? $seoUrl : new SeoUrl();

            if ((int)$language['language_id'] === (int)Language::getLanguageIdByCode(Yii::$app->language)) {
                $descriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }

        if ($model->load(Yii::$app->request->post())
            && RestaurantCategoryDescription::loadMultiple($descriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {
            $isValid = $model->validate();
            $isValid = RestaurantCategoryDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;

            if ($isValid && $model->save(false)) {
                // Update descriptions
                foreach ($descriptions as $key => $description) {
                    $description->restaurant_category_id = $model->restaurant_category_id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                $name = $descriptions[Language::getLanguageIdByCode(Yii::$app->language)]->name;

                /**
                 * @var int $key language id
                 * @var SeoUrl $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'restaurant_category_id=' . $model->restaurant_category_id;

                    if (empty($seoUrl->keyword)) {
                        $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($name), $key);
                    }

                    $seoUrl->save(false);
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
            'seoUrls' => $seoUrls,
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
        RestaurantCategoryDescription::removeByRestaurantCategoryId($id);
        SeoUrl::removeByQuery('restaurant_category_id=' . $id);

        return $this->goBack();
    }

    /**
     * @param integer $id
     * @return RestaurantCategory
     * @throws NotFoundHttpException
     */
    protected function findModel($id): RestaurantCategory
    {
        if (($model = RestaurantCategory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
