<?php

namespace app\module\admin\controllers;

use Exception;
use Throwable;
use Yii;
use app\components\ImageBehavior;
use app\module\admin\models\User;
use app\module\admin\models\Language;
use app\module\admin\models\PageDescription;
use app\module\admin\models\SeoUrl;
use app\module\admin\models\Page;
use app\module\admin\models\PageSearch;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class PageController extends Controller
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
        $searchModel = new PageSearch();
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
        /** @var Page|ImageBehavior $page */
        $page = new Page();

        $descriptions = [];
        $seoUrls = [];

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = new PageDescription();
            $seoUrl = new SeoUrl();

            if ((int)$language['language_id'] === (int)Language::getLanguageIdByCode(Yii::$app->language)) {
                $description->scenario = 'language-is-system';
            }

            $descriptions[$language['language_id']] = $description;
            $seoUrls[$language['language_id']] = $seoUrl;
        }


        if ($page->load(Yii::$app->request->post())
            && PageDescription::loadMultiple($descriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {

            $page->imageFile = UploadedFile::getInstance($page, 'imageFile');

            $isValid = $page->validate();

            if ($page->imageFile !== null) {
                $page->image = $page->uploadImage('imageFile');
            }

            $isValid = $page->validate('image') && $isValid;
            $isValid = PageDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;

            if ($isValid && $page->save(false)) {
                foreach ($descriptions as $key => $description) {
                    $description->page_id = $page->page_id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                // Save SEO URLs
                $pageName = $descriptions[Language::getLanguageIdByCode(Yii::$app->language)]->title;

                /**
                 * @var int $key language id
                 * @var SeoUrl $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'page_id=' . $page->page_id;
                    $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($pageName), $key);

                    $seoUrl->save(false);
                }

                return $this->goBack();
            }
        }

        if (empty($page->sort_order)) {
            $page->sort_order = 1;
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('create', [
            'page' => $page,
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
        /** @var Page|ImageBehavior $page */
        $page = $this->findModel($id);

        $descriptions = [];
        $seoUrls = [];

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = PageDescription::findOne([
                'page_id' => $page->page_id,
                'language_id' => $language['language_id']
            ]);

            $seoUrl = SeoUrl::findOne([
                'query' => 'page_id=' . $page->page_id,
                'language_id' => $language['language_id']
            ]);

            $descriptions[$language['language_id']] = (!empty($description)) ? $description : new PageDescription();
            $seoUrls[$language['language_id']] = (!empty($seoUrl)) ? $seoUrl : new SeoUrl();

            if ((int)$language['language_id'] === (int)Language::getLanguageIdByCode(Yii::$app->language)) {
                $descriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }


        if ($page->load(Yii::$app->request->post())
            && PageDescription::loadMultiple($descriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {

            $newImageFile = UploadedFile::getInstance($page, 'imageFile');

            if ($newImageFile !== null) {
                $page->removeImage($page->image); // Remove old image
                $page->imageFile = $newImageFile;
                $isValid = $page->validate();
                $page->image = $page->uploadImage('imageFile');
            } else {
                $isValid = $page->validate();
            }

            $isValid = PageDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;

            if ($isValid && $page->save(false)) {
                foreach ($descriptions as $key => $description) {
                    $description->page_id = $page->page_id;
                    $description->language_id = $key;
                    $description->save(false);
                }

                // Update SEO URLs
                $pageName = $descriptions[Language::getLanguageIdByCode(Yii::$app->language)]->title;

                /**
                 * @var int $key language id
                 * @var SeoUrl $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'page_id=' . $page->page_id;

                    if (empty($seoUrl->keyword)) {
                        $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($pageName), $key);
                    }

                    $seoUrl->save(false);
                }

                return $this->goBack();
            }
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        return $this->render('update', [
            'page' => $page,
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
        /** @var Page|ImageBehavior $model */
        $model = $this->findModel($id);
        $model->removeImage($model->image);

        SeoUrl::removeByQuery('page_id=' . $id);
        PageDescription::removeByPageId($id);
        $model->delete();

        return $this->goBack();
    }

    /**
     * @param integer $id
     * @return Page
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Page
    {
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
