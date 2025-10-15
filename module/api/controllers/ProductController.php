<?php

namespace app\module\api\controllers;

use app\module\admin\models\Classic;
use app\module\admin\models\SeoUrl;
use app\module\admin\module\product\models\CategoryPath;
use app\module\admin\module\product\models\Ingredient;
use app\module\admin\module\product\models\ProductToCategory;
use Yii;
use app\module\admin\module\product\models\Category;
use app\module\admin\module\product\models\Product;
use yii\data\Pagination;
use yii\db\Query;
use yii\filters\VerbFilter;

class ProductController extends BaseApiController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors[] = [
            'class' => VerbFilter::class,
            'actions' => [
                'categories' => ['GET'],
                'products-for-category' => ['GET'],
                'product' => ['GET'],
                'ingredients' => ['GET'],
                'classic' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @param string $lang
     * @param int $parent_id
     * @return array
     */
    public function actionCategories($lang, $parent_id = 0): array
    {
        Yii::$app->language = $lang;
        $restaurant_id = Yii::$app->request->get('restaurant_id') ?? 2; //Temporary solution for developing additional functionality time

        $query = Category::find()->where(['parent_id' => $parent_id, 'status' => Category::STATUS_ACTIVE]);

        if (!empty($restaurant_id)) {
            $query->andWhere(['restaurant_id' => $restaurant_id]);
        }

        $categories = $query->orderBy('sort_order ASC')->all();

        return [
            'status' => 'success',
            'data' => $categories
        ];
    }

    /**
     * @param string $lang
     * @param int $category_id
     * @param string $slug
     * @return array
     */
    public function actionProductsForCategory($lang, $category_id = null, string $slug = null): array
    {
        Yii::$app->language = $lang;

        $data = [];

        if ($slug) {
            $query = (new Query())
                ->select('p.product_id')
                ->from(CategoryPath::tableName() . ' AS cp')
                ->leftJoin(ProductToCategory::tableName() . ' AS p2c', 'cp.category_id = p2c.category_id')
                ->leftJoin(Category::tableName() . ' AS c', 'c.category_id = cp.category_id')
                ->leftJoin(Product::tableName() . ' AS p', 'p2c.product_id = p.product_id')
                ->leftJoin(SeoUrl::tableName() . ' AS su', 'su.query = CONCAT(\'category_id=\', c.category_id)')
                ->where(['su.keyword' => $slug, 'p.status' => Product::STATUS_ACTIVE])
                ->groupBy('p.product_id')
                ->orderBy('p.sort_order');
        } else {
            $query = (new Query())
                ->select('p.product_id')
                ->from(CategoryPath::tableName() . ' AS cp')
                ->leftJoin(ProductToCategory::tableName() . ' AS p2c', 'cp.category_id = p2c.category_id')
                ->leftJoin(Category::tableName() . ' AS c', 'c.category_id = cp.category_id')
                ->leftJoin(Product::tableName() . ' AS p', 'p2c.product_id = p.product_id')
                ->where(['cp.path_id' => $category_id, 'p.status' => Product::STATUS_ACTIVE])
                ->groupBy('p.product_id')
                ->orderBy('p.sort_order');
        }

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => Yii::$app->request->get('page_size') ?? 25,
            'page' => Yii::$app->request->get('page') - 1
        ]);

        $products = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        foreach ($products as $product) {
            $data[] = Product::findOne($product['product_id']);
        }

        return [
            'status' => 'success',
            'data' => $data,
            'meta' => [
                'total_pages' => $pagination->getPageCount(),
                'current_page' => $pagination->getPage() + 1,
                'page_size' => $pagination->getPageSize(),
            ]
        ];
    }

    /**
     * @param string $lang
     * @param int $product_id
     * @param string $slug
     * @return array
     */
    public function actionProduct($lang, $product_id = null, $slug = null): array
    {
        Yii::$app->language = $lang;

        if ($slug) {
            $su = SeoUrl::find()->where(['keyword' => $slug])->one();
            $product_id = preg_replace("/[^0-9]/", "", $su->query);
        }
        $product = Product::find()->where(['product_id' => $product_id, 'status' => Product::STATUS_ACTIVE])->one();

        if ($product) {
            return [
                'status' => 'success',
                'data' => $product,
            ];
        }

        Yii::$app->response->statusCode = 404;

        return [
            'status' => 'error',
            'message' => 'Product not found!'
        ];
    }

    /**
     * @param string $lang
     * @return array
     */
    public function actionAllProductsWithSuperOffer($lang): array
    {
        Yii::$app->language = $lang;

        $products = Product::find()->where(['super_offer' => '1', 'status' => Product::STATUS_ACTIVE])->all();

        if ($products) {
            return [
                'status' => 'success',
                'data' => $products,
            ];
        }

        Yii::$app->response->statusCode = 404;

        return [
            'status' => 'error',
            'message' => 'Product not found!'
        ];
    }


    /**
     * @param string $lang
     * @param int $category_id
     * @return array
     */
    public function actionIngredients($lang, $category_id): array
    {
        Yii::$app->language = $lang;

        return [
            'status' => 'success',
            'data' => Ingredient::find()
                ->where(['category_id' => $category_id, 'status' => Category::STATUS_ACTIVE])
                ->orderBy('sort_order ASC')
                ->all()
        ];
    }

    /**
     * @param string $lang
     * @return array
     */
    public function actionClassic($lang): array
    {
        Yii::$app->language = $lang;

        $classic = Classic::find()->where(['product_id' => 1])->one()->status;
        if($classic == 1) {
            $classic = Classic::findOne(1);

            if ($classic) {
                return [
                    'status' => 'success',
                    'data' => $classic,
                ];
            }
        } else {
            Yii::$app->response->statusCode = 404;

            return [
                'status' => 'error',
                'message' => 'Product not found!'
            ];
        }



        Yii::$app->response->statusCode = 404;

        return [
            'status' => 'error',
            'message' => 'Product not found!'
        ];
    }
}
