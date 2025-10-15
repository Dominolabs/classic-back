<?php

namespace app\module\admin\module\product\controllers;

use app\components\ImageBehavior;
use app\jobs\UploadPbFileJob;
use app\module\admin\models\SeoUrl;
use app\module\admin\models\User;
use app\module\admin\models\Language;
use app\module\admin\module\product\models\Ingredient;
use app\module\admin\module\product\models\ProductDescription;
use app\module\admin\module\product\models\ProductToCategory;
use app\module\admin\module\product\models\Product;
use app\module\admin\module\product\models\ProductSearch;
use app\module\admin\module\product\models\UploadPbFile;
use app\module\api\module\viber\controllers\helpers\Str;
use Exception;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class ProductController extends Controller
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
        $searchModel = new ProductSearch();
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
        /** @var Product | ImageBehavior $product */
        $product = new Product();

        $productToCategory = new ProductToCategory();

        $productDescriptions = [];
        $seoUrls = [];
        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $productDescription = new ProductDescription();
            $seoUrl = new SeoUrl();

            if ((int)$language['language_id'] === (int)Language::getLanguageIdByCode(Yii::$app->language)) {
                $productDescription->scenario = 'language-is-system';
            }

            $productDescriptions[$language['language_id']] = $productDescription;
            $seoUrls[$language['language_id']] = $seoUrl;
        }

        if ($product->load(Yii::$app->request->post())
            && $productToCategory->load(Yii::$app->request->post())
            && ProductDescription::loadMultiple($productDescriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {

            Yii::info('Loaded', 'viber');
            if (empty($product->price)) {
                $product->price = 0.0000;
            }
            if (empty($product->price2)) {
                $product->price2 = 0.0000;
            }

            $product->imageFile = UploadedFile::getInstance($product, 'imageFile');

            $isValid = $product->validate();
            Yii::info($isValid . '', 'viber');

            if ($product->imageFile !== null) {
                $product->image = $product->uploadImage();
            }

            $isValid = $product->validate('image') && $isValid;
            Yii::info($isValid . '', 'viber');

            $isValid = ProductDescription::validateMultiple($productDescriptions, Yii::$app->request->post()) && $isValid;
            Yii::info($isValid . '', 'viber');
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;
            Yii::info($isValid . '', 'viber');

            if ($isValid && $product->save(false)) {
                // Save product to category relation
                if (!empty($productToCategory->category_id)) {
                    $productToCategory->product_id = $product->product_id;
                    $productToCategory->save(false);
                }

                foreach ($productDescriptions as $key => $productDescription) {
                    $productDescription->product_id = $product->product_id;
                    $productDescription->language_id = $key;
                    $productDescription->save(false);
                }

                $name = $productDescriptions[Language::getLanguageIdByCode(Yii::$app->language)]->name;

                /**
                 * @var int $key language id
                 * @var SeoUrl $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'product_id=' . $product->product_id;
                    $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($name), $key);

                    $seoUrl->save(false);
                }

                return $this->goBack();
            }
        }
        else {
            Yii::info('Create', 'product');
            Yii::info((bool) $product->load(Yii::$app->request->post()), 'product');
            Yii::info((bool) $productToCategory->load(Yii::$app->request->post()), 'product');
            Yii::info((bool) ProductDescription::loadMultiple($productDescriptions, Yii::$app->request->post()), 'product');
            Yii::info((bool) SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post()), 'product');
        }

        if (empty($product->sort_order)) {
            $product->sort_order = 1;
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        $is_create = true;

        return $this->render('create', [
            'product' => $product,
            'productToCategory' => $productToCategory,
            'descriptions' => $productDescriptions,
            'languages' => $languages,
            'placeholder' => $placeholder,
            'seoUrls' => $seoUrls,
            'is_create' => $is_create
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception|Throwable
     */
    public function actionUpdate($id)
    {
        /**
         * @var Product|ImageBehavior $product
         */
        $product = $this->findModel($id);


        $productToCategoryModel = ProductToCategory::findOne([
            'product_id' => $id,
        ]);

        $productToCategory = $productToCategoryModel ?? new ProductToCategory();

        $productDescriptions = [];
        $seoUrls = [];

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = ProductDescription::findOne([
                'product_id' => $product->product_id,
                'language_id' => $language['language_id']
            ]);

            $seoUrl = SeoUrl::findOne([
                'query' => 'product_id=' . $product->product_id,
                'language_id' => $language['language_id']
            ]);

            $productDescriptions[$language['language_id']] = $description ?? new ProductDescription();
            $seoUrls[$language['language_id']] = $seoUrl ?? new SeoUrl();

            if ((int)$language['language_id'] === (int)Language::getLanguageIdByCode(Yii::$app->language)) {
                $productDescriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }

        if ($product->load(Yii::$app->request->post())
            && $productToCategory->load(Yii::$app->request->post())
            && ProductDescription::loadMultiple($productDescriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {

//            dd(Yii::$app->request->post());

            if (empty($product->price)) {
                $product->price = 0.0000;
            }
            if (empty($product->price2)) {
                $product->price2 = 0.0000;
            }

            $newImageFile = UploadedFile::getInstance($product, 'imageFile');

            if ($newImageFile !== null) {
                $product->removeImage($product->image); // Remove old image
                $product->imageFile = $newImageFile;

                $isValid = $product->validate();

                $product->image = $product->uploadImage();
            } else {
                $isValid = $product->validate();
            }

            Yii::info($product->image, 'product');

            $isValid = ProductDescription::validateMultiple($productDescriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;

            if ($isValid && $product->save(false)) {
                $categoryId = $productToCategory->category_id;
                ProductToCategory::removeByProductId($product->product_id);

                if (!empty($categoryId)) {
                    $newProductToCategory = new ProductToCategory();
                    $newProductToCategory->category_id = $productToCategory->category_id;
                    $newProductToCategory->product_id = $product->product_id;

                    $newProductToCategory->save(false);
                }

                foreach ($productDescriptions as $key => $productDescription) {
                    $productDescription->product_id = $product->product_id;
                    $productDescription->language_id = $key;
                    $productDescription->save(false);
                }

                $name = $productDescriptions[Language::getLanguageIdByCode(Yii::$app->language)]->name;

                /**
                 * @var int $key language id
                 * @var SeoUrl $seoUrl category SEO URL
                 */
                foreach ($seoUrls as $key => $seoUrl) {
                    $seoUrl->language_id = $key;
                    $seoUrl->query = 'product_id=' . $product->product_id;

                    if (empty($seoUrl->keyword)) {
                        $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($name), $key);
                    }

                    $seoUrl->save(false);
                }

                return $this->goBack();
            }
        }
        else {
            Yii::info('Update id = ' . $id, 'product');
            Yii::info((bool) $product->load(Yii::$app->request->post()), 'product');
            Yii::info((bool) $productToCategory->load(Yii::$app->request->post()), 'product');
            Yii::info((bool) ProductDescription::loadMultiple($productDescriptions, Yii::$app->request->post()), 'product');
            Yii::info((bool) SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post()), 'product');
        }

        $placeholder = ImageBehavior::placeholder(100, 100);

        $is_create = false;
        return $this->render('update', [
            'product' => $product,
            'productToCategory' => $productToCategory,
            'descriptions' => $productDescriptions,
            'languages' => $languages,
            'placeholder' => $placeholder,
            'seoUrls' => $seoUrls,
            'is_create' => $is_create
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

        ProductToCategory::removeByProductId($id);
        ProductDescription::removeByProductId($id);
        SeoUrl::removeByQuery('product_id=' . $id);

        return $this->goBack();
    }

    /**
     * @param integer $id
     * @return Product
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }

    public function actionUploadPb()
    {
        header('Content-Type: application/json');
        try {
            $file = UploadedFile::getInstanceByName('file');
            if (!$file) {
                throw new Exception('Файл не найден');
            }
            $id = Str::random();
            $dir = Yii::getAlias('@app/runtime/pb/');
            is_dir($dir) || mkdir($dir);
            $name = Yii::getAlias('@app/runtime/pb/' . $id . '.' . $file->extension);
            if (!$file->saveAs($name)) {
                throw new Exception("Не удалось сохранить файл");
            }
            $model = new UploadPbFile();
            $model->file = $name;
            $model->status = UploadPbFile::STATUS_NEW;
            if (!$model->save()) {
                throw new Exception(json_encode($model->getErrors()));
            }
            Yii::$app->queue->push(new UploadPbFileJob([
                'id' => $model->id,
                'modelClass' => ($_POST['type' ?? null]) === 'ingredient' ? Ingredient::class : Product::class
            ]));

            $this->returnJson(['id' => $model->id]);
        } catch (Throwable $e) {
            $this->returnJson(['message' => $e->getMessage()]);
        }
    }

    protected function returnJson(array $response, $statusCode = 200)
    {
        Yii::$app->response->setStatusCode($statusCode);
        Yii::$app->response->format = Yii::$app->response::FORMAT_JSON;
        Yii::$app->response->data = $response;
        Yii::$app->response->send();
        Yii::$app->end();
    }

    public function actionUploadPbStatus()
    {
        header('Content-Type: application/json');
        try {
            $id = Yii::$app->request->get('id');
            if (empty($id)) {
                throw new Exception('Файл не найден', 404);
            }
            $model = UploadPbFile::findOne(['id' => $id]);
            if (empty($model)) {
                throw new Exception('Файл не найден', 404);
            }
            do {
                if ($model->status) {
                    break;
                }
                usleep(750);
                $model->refresh();
                $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
            } while ($executionTime < 20);
            if ($model->status) {
                $model->delete();
                if ($model->status == UploadPbFile::STATUS_ERROR) {
                    throw new Exception($model->message ?? 'Что-то пошло не так');
                }
                $this->returnJson(['message' => $model->message]);
            } else {
                $this->returnJson(['id' => $model->id, 'message' => $model->message ?? 'Файл обрабатывется']);
            }
        } catch (Throwable $e) {
            $this->returnJson(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
