<?php

namespace app\module\admin\module\order\controllers;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Throwable;
use Yii;
use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\product\models\Product;
use app\module\admin\module\order\models\OrderProduct;
use app\module\admin\module\order\models\OrderProductSearch;
use app\module\admin\models\User;
use app\module\admin\module\order\models\OrderHistory;
use app\module\admin\module\order\models\OrderHistorySearch;
use app\module\admin\module\order\models\Order;
use app\module\admin\module\order\models\OrderSearch;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\traits\PrintTrait;

class OrderController extends Controller
{
    use PrintTrait;

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
     * @throws NotFoundHttpException
     * @throws Exception|Throwable
     * @throws StaleObjectException
     */
    public function actionIndex()
    {
        $this->removeUnusedOrder();

        $deleted_only = Yii::$app->request->get('is_deleted');
        $searchModel = new OrderSearch();
        $dataProvider = $deleted_only
            ? $searchModel->search(Yii::$app->request->queryParams, 1)
            : $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'deleted_only' => $deleted_only ? true : false
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
        $model = $this->findModel($id);

        $orderProductSearchModel = new OrderProductSearch();
        $orderProductDataProvider = $orderProductSearchModel->search(['OrderProductSearch' => [
            'order_id' => $id,
        ]]);

        $orderHistoryModel = new OrderHistory();
        $orderHistorySearchModel = new OrderHistorySearch();
        $orderHistoryDataProvider = $orderHistorySearchModel->search(['OrderHistorySearch' => [
            'order_id' => $id,
        ]]);

        if ($model->load(Yii::$app->request->post())) {
//            $timezone = new DateTimeZone('Europe/Kiev'); // Вказуємо часовий пояс
//            $dateTime = new DateTime('now', $timezone); // Поточний час у цьому часовому поясі

//            $offsetInSeconds = $timezone->getOffset($dateTime);
//            $offsetInHours = $offsetInSeconds / 3600;

            $time = Carbon::parse($model->time)->addHours(-3);
            $model->time = strtotime($time);
            if ($model->save()) {
                return $this->goBack();
            }
        }

        $lastOrderHistory = OrderHistory::find()->where(['order_id' => $id])->orderBy(['order_history_id' => SORT_DESC])->one();

        return $this->render('update', [
            'model' => $model,
            'orderProductDataProvider' => $orderProductDataProvider,
            'orderHistoryModel' => $orderHistoryModel,
            'orderHistoryDataProvider' => $orderHistoryDataProvider,
            'lastOrderHistory' => $lastOrderHistory ?? null
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
//        $this->findModel($id)->delete();
        $this->findModel($id)->softDelete();

        OrderProduct::removeByOrderId($id);
        OrderHistory::removeByOrderId($id);

        return $this->goBack();
    }

    /**
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPrint($id)
    {
        /** @var Order $order */
        $order = $this->findModel($id);

        if ($order) {
            $text = $this->renderPartial('@app/printer/order-text', [
                'order' => $order,
                'orderProducts' => $order->orderProducts,
            ]);
            $this->printOrder($order, base64_encode($text));
        }

        return $this->goBack();
    }

    /**
     * @param int $userId
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionUserData($userId): array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return User::getById($userId);
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }

    /**
     * @param int $order_id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionGetBalance($order_id): ?array
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $order = Order::find()->where(['order_id' => $order_id])->with(['orderProducts' => static function ($q) {
                $q->where(['weight_dish' => OrderProduct::YES]);
            }])->one();

            if($order){
                $response = [
                    'packing' => Currency::format($order->packing, 'UAH'),
                    'delivery' => Currency::format($order->delivery, 'UAH'),
                    'sum' => Currency::format($order->sum, 'UAH'),
                    'cityName' => $order->getCityName(),
                    'payment_type' => Order::getPaymentTypeName($order->payment_type),
                    'payment_type_code' => $order->payment_type,
                    'online_payment_sum' => (float)$order->total_for_online_payment,
                    'promotions_applied' => (bool) $order->promotions_applied,
                ];

                if ($response['promotions_applied']) {
                    $response['applied_promotions'] = json_decode($order->promotions_applied, true);
                }

                if (count($order->orderProducts) > 0) {
                    $response['has_weight_dishes'] = true;
                    $weight_dishes_packing_price = 0;
                    $weight_dishes_products_price = 0;

                    foreach($order->orderProducts as $product){
                        $product_db = Product::findOne($product->product_id);
                        if($product->product_type === 1){
                            $price = (float)$product_db->price;
                            $packaging_price = (float)$product_db->packaging_price;
                        } else {
                            $price = (float)$product_db->price2;
                            $packaging_price = (float)$product_db->packaging_price2;
                        }
                        $weight_dishes_packing_price += $packaging_price;
                        $weight_dishes_products_price += $price * $product->quantity;
                    }
                    $response['weight_dishes_products_price'] = $weight_dishes_products_price;
                    $response['weight_dishes_packaging_price'] = $weight_dishes_packing_price;
                }

                return $response;
            }

        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }

    /**
     * @param integer $id
     * @return Order
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Order
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }

    /**
     * @throws NotFoundHttpException
     * @throws Exception|Throwable
     * @throws StaleObjectException
     */
    protected function removeUnusedOrder(): void
    {
        $session = Yii::$app->session;

        if ($session->has('OrderProduct_order_id')) {
            $orderId = $session->get('OrderProduct_order_id');

            $this->findModel($orderId)->delete();

            OrderProduct::removeByOrderId($orderId);
            OrderHistory::removeByOrderId($orderId);

            $session->remove('OrderProduct_order_id');
        }
    }
}
