<?php
/**
 * OrderProductController class file.
 */

namespace app\module\admin\module\order\controllers;

use Yii;
use app\module\admin\models\User;
use app\module\admin\module\order\models\Order;
use app\module\admin\models\Language;
use app\module\admin\module\product\models\Product;
use app\module\admin\module\product\models\ProductDescription;
use app\module\admin\module\order\models\OrderProduct;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * OrderProductController implements the CRUD actions for OrderProduct model.
 */
class OrderProductController extends Controller
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
     * Creates a new OrderProduct model.
     *
     * @throws NotFoundHttpException if request is not Ajax
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isAjax) {
            $model = new OrderProduct();

            $model->load(Yii::$app->request->post());

            if (!empty($model->order_id) && !empty($model->product_id)) {
                $order = Order::findOne(['order_id' => $model->order_id]);
                $product = Product::findOne(['product_id' => $model->product_id]);

                if (!empty($order) && !empty($product)) {
                    $model->name = $product->getProductName();
                    $model->price = $product->price;
                    $model->total = $product->price * $model->quantity;

                    if ($model->save()) {
                        $order->updateTotal();
                    }
                }
            }
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

    /**
     * Deletes an existing OrderProduct model.
     *
     * @param integer $id model id
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception|\Throwable in case delete failed.
     * @throws \yii\db\StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     */
    public function actionDelete($id)
    {
        $orderProduct = $this->findModel($id);

        $order = Order::findOne(['order_id' => $orderProduct->order_id]);

        $orderProduct->delete();

        if (!empty($order)) {
            $order->updateTotal();
        }
    }

    /**
     * Finds the OrderProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderProduct::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

    /**
     * Returns products list.
     *
     * @param null|string $q query string
     * @param null|int $id product id
     * @return array products list data
     * @throws NotFoundHttpException if request is not Ajax
     */
    public function actionProductsList($q = null, $id = null)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = ['results' => ['id' => '', 'text' => '', 'price' => '']];

            if (!is_null($q)) {
                $data = [];

                $products = (new Query())->select('p.product_id AS id, pd.name AS text, p.price AS price')
                    ->from(Product::tableName() . ' AS p')
                    ->leftJoin(ProductDescription::tableName() . ' AS pd',
                        'p.product_id = pd.product_id AND pd.language_id = ' . Language::getLanguageIdByCode(Yii::$app->language)
                    )
                    ->where(['like', 'pd.name', $q])
                    ->groupBy('p.product_id')
                    ->limit(10)
                    ->orderBy('pd.name ASC')
                    ->all();

                foreach ($products as $product) {
                    $data[] = $product;
                }

                $result['results'] = array_values($data);
            } elseif ($id > 0) {
                $product = Product::findOne(['product_id' => $id]);

                $result['results'] = [
                    'id' => $id,
                    'text' => $product->getProductName(),
                    'price' => $product->price
                ];
            }

            return $result;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }
}
