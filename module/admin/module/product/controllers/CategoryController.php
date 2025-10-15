<?php

namespace app\module\admin\module\product\controllers;

use app\module\admin\models\SeoUrl;
use app\module\admin\module\product\models\Product;
use app\module\admin\module\product\models\ProductSearch;
use Yii;
use app\module\admin\models\User;
use app\module\admin\module\product\models\ProductToCategory;
use app\components\ImageBehavior;
use app\module\admin\models\Language;
use app\module\admin\module\product\models\CategoryDescription;
use app\module\admin\module\product\models\Category;
use app\module\admin\module\product\models\CategorySearch;
use app\module\admin\module\product\models\CategoryPath;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class CategoryController extends Controller
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
     * Lists all Category models.
     *
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        /** @var Category|ImageBehavior $category */
        $category = new Category();

        $descriptions = [];
        $seoUrls = [];

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = new CategoryDescription();
            $seoUrl = new SeoUrl();

            if ((int)$language['language_id'] === (int)Language::getLanguageIdByCode(Yii::$app->language)) {
                $description->scenario = 'language-is-system';
            }

            $descriptions[$language['language_id']] = $description;
            $seoUrls[$language['language_id']] = $seoUrl;
        }

        if ($category->load(Yii::$app->request->post())
            && CategoryDescription::loadMultiple($descriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {

            if (empty($category->parent_id)) {
                $category->parent_id = 0;
            }

            $category->imageFile = UploadedFile::getInstance($category, 'imageFile');

            $isValid = $category->validate();

            if ($category->imageFile !== null) {
                $category->image = $category->uploadImage();
            }

            $isValid = $category->validate('image') && $isValid;

            $isValid = CategoryDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;

            if ($isValid && $category->save(false)) {
                // Save category path
                $category->saveCategoryPath();

                //Adopt category packaging price to related products
                try {
                    $this->handlePackagingPrice($category, Yii::$app->request->post());
                } catch (\Throwable $exception) {
                    return $this->render('update', [
                        'category' => $category,
                        'descriptions' => $descriptions,
                        'languages' => $languages,
                        'seoUrls' => $seoUrls,
                        'error' => $exception->getMessage()
                    ]);
                }

                // Save descriptions
                foreach ($descriptions as $key => $description) {
                    $description->category_id = $category->category_id;
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
                    $seoUrl->query = 'category_id=' . $category->category_id;
                    $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($name), $key);

                    $seoUrl->save(false);
                }

                $operation_id = Yii::$app->request->post('operation_id') ?: -1;
                Yii::$app->db->createCommand('UPDATE tbl_category_top_products SET `category_id`=' . $category->category_id . ' where `operation_id`=:op_id AND `category_id` IS NULL')
                    ->bindValue('op_id', $operation_id)->execute();
                return $this->goBack();
            }
        }

        if (empty($category->sort_order)) {
            $category->sort_order = 1;
        }

        if (empty($category->top)) {
            $category->top = Category::YES;
        }

        $searchModel = new ProductSearch();

        if (empty($string = Yii::$app->request->get('_pjax'))) {
            $operation_id = (int)(new Query())->select('max(`operation_id`) as max from {{%category_top_products}}')->one()['max'] ?? 0;
            $operation_id++;
        } else {
            $operation_id = (int)str_replace('#top-product-pjax-container-op_id-', '', $string);
        }

        $ids = (new Query())->select('*')->from('{{%category_top_products}}')
            ->where(['operation_id' => $operation_id, 'category_id' => null])->all();
        $ids = array_column($ids, 'product_id');
        $query = Product::find()->where(['in', '{{%product}}.product_id', $ids]);


        return $this->render('create', [
            'category' => $category,
            'descriptions' => $descriptions,
            'languages' => $languages,
            'seoUrls' => $seoUrls,
            'error' => '',
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams, $query),
            'topProduct' => new Product(),
            'operation_id' => $operation_id,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionUpdate($id)
    {
        /** @var Category|ImageBehavior $category */
        $category = $this->findModel($id);

        $descriptions = [];
        $seoUrls = [];

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $description = CategoryDescription::findOne([
                'category_id' => $category->category_id,
                'language_id' => $language['language_id']
            ]);

            $seoUrl = SeoUrl::findOne([
                'query' => 'category_id=' . $category->category_id,
                'language_id' => $language['language_id']
            ]);

            $descriptions[$language['language_id']] = $description ?? new CategoryDescription();
            $seoUrls[$language['language_id']] = $seoUrl ?? new SeoUrl();

            if ((int)$language['language_id'] === (int)Language::getLanguageIdByCode(Yii::$app->language)) {
                $descriptions[$language['language_id']]->scenario = 'language-is-system';
            }
        }

        if ($category->load(Yii::$app->request->post())
            && CategoryDescription::loadMultiple($descriptions, Yii::$app->request->post())
            && SeoUrl::loadMultiple($seoUrls, Yii::$app->request->post())) {

            if (empty($category->parent_id)) {
                $category->parent_id = 0;
            }

            $newImageFile = UploadedFile::getInstance($category, 'imageFile');

            if (!empty($newImageFile)) {
                $category->removeImage($category->image); // Remove old image
                $category->imageFile = $newImageFile;

                $isValid = $category->validate();

                $category->image = $category->uploadImage();
            } else {
                $isValid = $category->validate();
            }

            $isValid = CategoryDescription::validateMultiple($descriptions, Yii::$app->request->post()) && $isValid;
            $isValid = SeoUrl::validateMultiple($seoUrls, Yii::$app->request->post()) && $isValid;

            if ($isValid && $category->save(false)) {
                // Update category path
                $category->updateCategoryPath();

                //Adopt category packaging price to related products
                try {
                    $this->handlePackagingPrice($category, Yii::$app->request->post());
                } catch (\Throwable $exception) {
                    return $this->render('update', [
                        'category' => $category,
                        'descriptions' => $descriptions,
                        'languages' => $languages,
                        'seoUrls' => $seoUrls,
                        'error' => $exception->getMessage()
                    ]);
                }

                // Update descriptions
                foreach ($descriptions as $key => $description) {
                    $description->category_id = $category->category_id;
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
                    $seoUrl->query = 'category_id=' . $category->category_id;

                    if (empty($seoUrl->keyword)) {
                        $seoUrl->keyword = SeoUrl::prepare(SeoUrl::transliterate($name), $key);
                    }

                    $seoUrl->save(false);
                }

                return $this->goBack();
            }
        }

        $searchModel = new ProductSearch();

        return $this->render('update', [
            'category' => $category,
            'descriptions' => $descriptions,
            'languages' => $languages,
            'seoUrls' => $seoUrls,
            'error' => '',
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams, $category->getTopProducts()),
            'topProduct' => new Product(),
            'operation_id' => null,
        ]);
    }


    /**
     * @param $category
     * @param $post_data
     * @throws Exception
     */
    protected function handlePackagingPrice($category, $post_data): void
    {
        if (isset($post_data['apply_packaging_price_to_products']) && !empty($post_data['apply_packaging_price_to_products'])) {
            try {
                $packing_price = $category->packing_price;
                $products_with_variants = $category->variantsProducts;

                if (!empty($products_with_variants)) {
                    $products_with_variants_ids = array_values(array_column($products_with_variants, 'product_id'));
                    Product::updateAll(['packaging_price' => $packing_price, 'packaging_price2' => $packing_price], ['in', 'product_id', $products_with_variants_ids]);
                }

                $products_with_no_variants = $category->noVariantsProducts;
                if (!empty($products_with_no_variants)) {
                    $products_ids = array_values(array_column($products_with_no_variants, 'product_id'));
                    Product::updateAll(['packaging_price' => $packing_price], ['in', 'product_id', $products_ids]);
                }
            } catch (\Throwable $exception) {
                throw new Exception('Приминение стоимости упаковки ко всем товарам категории не удалось выполнить');
            }
        }
    }


    /**
     * Deletes an existing Category model.
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
        $this->findModel($id)->delete();

        CategoryPath::removeByCategoryId($id);
        ProductToCategory::removeByCategoryId($id);
        CategoryDescription::removeByCategoryId($id);
        SeoUrl::removeByQuery('category_id=' . $id);
        Yii::$app->db->createCommand('DELETE from {{%category_top_products}} where `category_id`=' . $id)->execute();

        return $this->goBack();
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionAddTopProduct()
    {
        if (Yii::$app->request->isAjax) {
            if (Yii::$app->request->post('product_id') == 0) return;
            /** @var Product $product */
            $product = Product::find()->where(['product_id' => Yii::$app->request->post('product_id')])->one();
            if (!$product) throw new NotFoundHttpException('Продукт не найден.');
            if (empty($cat_id = (int)Yii::$app->request->post('category_id'))) {
                if (empty($op_id = (int)Yii::$app->request->post('operation_id')) || !is_int($op_id)) return;
                $existing = (new Query())->select('*')->from('{{%category_top_products}}')
                    ->where(['product_id' => $product->product_id,
                        'operation_id' => $op_id])->all();
                if (!$existing)
                    Yii::$app->db
                        ->createCommand('INSERT INTO {{%category_top_products}} (`product_id`, `operation_id`) VALUES (' . $product->product_id . ', :operation_id)')
                        ->bindValue(':operation_id', $op_id)
                        ->execute();
            } else {
                $category = Category::find()->with('topProducts')
                    ->where(['category_id' => $cat_id])->one();
                if (!$category) throw new NotFoundHttpException('Категория не найдена');
                /** @var Category $category */
                if ($category->getTopProducts()->where(['product_id' => $product->product_id])->exists()) return;
                $category->link('topProducts', $product);
            }
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

    /**
     * @param $operation_id
     * @param $category_id
     * @param $product_id
     * @throws Exception
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionDeleteTopProduct($operation_id, $category_id, $product_id)
    {
        if (Yii::$app->request->isAjax) {
            $product = Product::find()->where(['product_id' => $product_id])->one();
            if (!$product) throw new NotFoundHttpException('Продукт не найден.');
            /** @var Product $product */
            if (empty((int) $operation_id)) {
                $category = Category::findOne($category_id);
                if (!$category) throw new NotFoundHttpException('Категория не найденаю');
                /** @var Category $category */
                if ($category->getTopProducts()->where(['product_id' => $product->product_id])->exists())
                    $category->unlink('topProducts', $product);
            } else {
                Yii::$app->db->createCommand('DELETE FROM {{%category_top_products}} where `category_id` IS NULL and `product_id`=' . $product->product_id)
                    ->execute();
            }

        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Category id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
