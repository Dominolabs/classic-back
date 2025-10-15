<?php

namespace app\module\api\controllers;

use app\components\cart\CartPositionInterface;
use app\components\cart\CartPositionTrait;
use app\components\ImageBehavior;
use app\jobs\SendNewOrderEmailJob;
use app\models\SetOrderRatingForm;
use app\module\admin\models\Classic;
use app\module\admin\models\SettingForm;
use app\module\admin\module\event\models\Tag;
use app\module\admin\module\order\models\City;
use app\module\admin\module\order\models\OrderSearch;
use app\module\admin\module\product\models\Category;
use app\module\admin\module\product\models\Ingredient;
use app\module\admin\module\product\models\ProductToCategory;
use app\module\admin\module\order\models\OrderHistory;
use app\module\admin\module\order\models\OrderProduct;
use app\module\admin\module\product\models\Product;
use app\components\cart\ShoppingCart;
use app\module\admin\models\Language;
use app\module\admin\models\User;
use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\order\models\Order;
use app\module\admin\module\order\models\OrderForm;
use app\module\api\controllers\traits\ResponseTrait;
use app\module\api\exceptions\NotFoundException;
use app\module\api\exceptions\ValidationException;
use app\module\api\jobs\PrintOrderJob;
use app\module\api\module\viber\controllers\handlers\tracking_data\traits\OrderInfoTrait;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\senders\ViberSender;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use SendEmailJob;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class OrderController extends BaseApiController
{
    use ResponseTrait;

    public $orderHasWeightDish = false;
    public $sum = 0;
    public $packing = 0;

    public function init() :void
    {
        parent::init();
        \Yii::$app->setTimeZone('Europe/Kyiv');
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors[] = [
            'class' => VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'history' => ['GET'],
                'cities' => ['GET'],
                'set-rating' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @param string $lang
     * @return array
     */
    public function actionCreate($lang): array
    {
        $transaction = Order::getDb()->beginTransaction();
        try {
            /* @var User $user */
            $user = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);
            $products = $this->getProductsFromCart(Yii::$app->request->post('cart'));
            if (!empty($products)) {
                $order = $this->prepareOrder(['user' => $user]);
                if (Yii::$app->request->post('restaurant')) {
                    $order->restaurant_id = Yii::$app->request->post('restaurant');
                }
                $order->save(false);
                foreach ($products as &$product) {
                    $product = array_merge($product, ['order_id' => $order->order_id]);
                    $OP = new OrderProduct($product);
                    if (!$OP->validate()) throw new ValidationException($OP->getErrors());
                    $OP->save();
                    $orderProducts[] = $OP;
                }
                $this->saveToHistory($order);

                $transaction->commit();


                Yii::$app->queue->push(new SendNewOrderEmailJob([
                    'order_id' => $order->order_id
                ]));

                $this->broadcastOnViber($order);

                $this->print($order);
            }

            if (!empty($order) && $order->payment_type == Order::PAYMENT_TYPE_ONLINE) {
                return self::jsonResponse([
                    'status' => 200,
                    'message' => Yii::t('order', 'Ваше замовлення успішно оформлене!'),
                    'payment_url' => Url::to(['payment?order_id=' . $order->order_id . '&version=10'], 'https')
                ]);
            }
            return self::jsonResponse([
                'status' => 200,
                'message' => Yii::t('order', 'Ваше замовлення успішно оформлене!')
            ]);
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::info([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 'printer');
            return self::handleException($e);
        }
    }

    /**
     * @param $data
     * @return float|int
     */
    private function getWeightDishesPrice($data)
    {
        $total = 0;
        if (!empty($data)) {
            foreach ($data as $value) {
                $total += ($value['price'] * $value['quantity']) + $value['packaging_price'] + $value['extra_ingredients_value'];
            }
        }
        return $total;
    }

    /**
     * @param string $lang
     * @return array
     * @throws InvalidConfigException
     */
    public function actionHistory($lang): array
    {
        Yii::$app->language = $lang;
        $limit = Yii::$app->request->get('limit') ?? 8;

        /* @var User $user */
        $user = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);

        if ($user !== null) {
            $data = [];
            $searchModel = new OrderSearch();
            $pizzaCategoryId = (int)Yii::$app->params['pizzaCategoryId'] ?: 0;

            $dataProvider = $searchModel->search([]);
            $dataProvider->query->where(['user_id' => $user->user_id]);
            $dataProvider->pagination->pageSize = $limit;
            $dataProvider->pagination->page = Yii::$app->request->get('page') - 1;

            /** @var Order $model */
            foreach ($dataProvider->getModels() as $model) {
                $orderProductsData = $model->orderProducts;
                $model->created_at = Yii::$app->formatter->asDate($model->created_at, 'd MMMM yyyy');
                $model->updated_at = Yii::$app->formatter->asDate($model->updated_at, 'd MMMM yyyy');
                foreach ($orderProductsData as $key => $dataItem) {
                    $categoryId = ProductToCategory::getCategoryIdByProductId($dataItem->product_id);
                    $category = Category::getCategory($categoryId);
                    if ($dataItem->type === 'classic') {
                        $product = Classic::find()->where(['product_id' => $dataItem->product_id])->with('productDescription')->one();
                    } else {
                        $product = Product::find()->where(['product_id' => $dataItem->product_id])->with('productDescription')->one();
                    }
                    $product_data = [];
                    if (!empty($product)) {
                        $product_data['image'] = $this->getProductImage($product, true);
                        $product_data['image_preview'] = $this->getProductImage($product, false);
                        $product_data['description'] = htmlspecialchars_decode(strip_tags($product->description), ENT_QUOTES);
                        $product = $product->attributes;
                        $product['image'] = $product_data['image'];
                        $product['image_preview'] = $product_data['image_preview'];
                        $product['description'] = $product_data['description'];
                        $product['thumb'] = ImageBehavior::getThumbnailFileName($product['image'], 600, 600);
                    } else {
                        $product = [];
                    }
                    $orderProductsData[$key] = ArrayHelper::merge($dataItem->toArray(), [
                        'product_category' => $category,
                        'product' => $product
                    ]);
                }
                $ordersData = ArrayHelper::merge($model->toArray(), [
                    'quantity' => $model->getProductsCount(),
                    'status_text' => Order::getStatusName($model->status),
                    'payment_type_text' => Order::getPaymentTypeName($model->payment_type),
                    'order_products' => $orderProductsData
                ]);
                if ((int)$model->payment_type === Order::PAYMENT_TYPE_ONLINE
                    && (int)$model->payment_status === Order::PAYMENT_STATUS_NOT_PAID
                    && $model->total > 0) {
                    $ordersData = ArrayHelper::merge($ordersData, [
                        'payment_url' => Url::to(['payment?order_id=' . $model->order_id], true)
                    ]);
                }
                $data[] = $ordersData;
            }

            return [
                'status' => 'success',
                'data' => $data,
                'meta' => [
                    'total_pages' => $dataProvider->pagination->getPageCount(),
                    'current_page' => $dataProvider->pagination->getPage() + 1,
                    'page_size' => $dataProvider->pagination->getPageSize(),
                ]
            ];
        }

        return [
            'status' => 'error',
            'message' => Yii::t('api', 'Запитуваний користувач не знайдений.')
        ];
    }

    public function actionHistoryShow($lang, $id)
    {
        Yii::$app->language = $lang;
        /* @var User $user */
        $user = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);


        if (!$user) {
            Yii::$app->response->statusCode = 422;
            return [
                'status' => 'error',
                'message' => Yii::t('product', 'Запитуваний користувач не знайдене.')
            ];
        }


        $model = Order::find()
            ->with(['orderProducts'])
            ->where(['order_id' => $id, 'user_id' => $user->user_id])->one();

        if (!$model) {
            Yii::$app->response->statusCode = 422;
            return [
                'status' => 'error',
                'message' => Yii::t('product', 'Запитуване замовлення не знайдений.')
            ];
        }

        $pizzaCategoryId = (int)Yii::$app->params['pizzaCategoryId'] ?: 0;

        $model->created_at = Yii::$app->formatter->asDate($model->created_at, 'd MMMM yyyy');
        $model->updated_at = Yii::$app->formatter->asDate($model->updated_at, 'd MMMM yyyy');
        $orderProductsData = $model->orderProducts;

        foreach ($orderProductsData as $key => $dataItem) {
            if ($dataItem->type === 'classic') {
                $categoryId = $pizzaCategoryId;
                $product = Classic::findOne($dataItem->product_id);
            } else {
                $categoryId = ProductToCategory::getCategoryIdByProductId($dataItem->product_id);
                $product = Product::findOne($dataItem->product_id);
            }
            $category = Category::getCategory($categoryId);

            $orderProductsData[$key] = ArrayHelper::merge($dataItem->toArray(), [
                'product_category' => $category['name'],
                'product' => $product
            ]);
        }

        $orderData = ArrayHelper::merge($model->toArray(), [
            'quantity' => $model->getProductsCount(),
            'status_text' => Order::getStatusName($model->status),
            'payment_type_text' => Order::getPaymentTypeName($model->payment_type),
            'order_products' => $orderProductsData
        ]);


        return [
            'status' => 'success',
            'order' => $orderData
        ];
    }

    /**
     * @param string $lang
     * @return array
     */
    public function actionCities($lang): array
    {
        Yii::$app->language = $lang;

        return [
            'status' => 'success',
            'data' => City::find()
                ->where(['status' => City::STATUS_ACTIVE])
                ->orderBy('sort_order ASC')
                ->all(),
        ];
    }

    /**
     * @param string $lang language code
     * @return array response data
     */
    public function actionSetRating($lang)
    {
        Yii::$app->language = $lang;

        /* @var User|ImageBehavior $user */
        $user = User::findOne(['auth_key' => Yii::$app->request->headers->get('Auth-Key')]);

        if ($user !== null) {
            $starRatingForm = new SetOrderRatingForm();
            $starRatingForm->attributes = Yii::$app->request->post();

            if ($starRatingForm->validate()) {
                /** @var Order $order */
                $order = Order::find()->where(['order_id' => $starRatingForm->order_id, 'user_id' => $user->user_id])->one();
                if ($order) {
                    $order->rating = $starRatingForm->rating;
                    if ($order->save(false)) {
                        return [
                            'status' => 'success',
                            'message' => Yii::t('api', 'Оцінку успішно встановлено.')
                        ];
                    }
                } else {
                    Yii::$app->response->statusCode = 422;

                    return [
                        'status' => 'error',
                        'message' => Yii::t('api', 'Замовлення не знайдено.')
                    ];
                }
            }

            Yii::$app->response->statusCode = 422;

            $response = [
                'status' => 'error',
            ];
            foreach ($starRatingForm->getErrors() as $attribute => $errors) {
                $response['errors'][$attribute] = $errors[0];
            }

            return $response;
        }

        Yii::$app->response->statusCode = 422;

        return [
            'status' => 'error',
            'message' => Yii::t('api', 'Запитуваний користувач не знайдений.')
        ];
    }


    //==PRIVATE==//
    private function getProductImage($product, $full = false)
    {
        if (!empty($product->image) && file_exists($product->getImagePath() . DIRECTORY_SEPARATOR . $product->image)) {
            return $full ? BaseApiController::BASE_SITE_URL . 'image/product/' . $product->image : BaseApiController::BASE_SITE_URL . trim($product->resizeImage($product->image, 300, 300), '/');
        }

        return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
    }

    /**
     * @param $cart
     * @return array
     * @throws ValidationException
     */
    private function getProductsFromCart($cart)
    {
        if (!is_array($cart)) throw new ValidationException(
            ["cart" => [Yii::t('validation', "Cart should be an array.")]]
        );

        $result = [];
        if (empty($cart)) return $result;
        foreach ($cart as $key => $pos) {
            if (empty($pos) || !is_array($pos)) continue;
            $product = $this->getProduct($key, $pos);
            $large = !empty($pos['size']) && $pos['size'] == 2;
            // If product is weight dish, we should not count delivery price, so we need to define if there any
            // dish product in the cart.
            $this->orderHasWeightDish = $this->orderHasWeightDish || ($product->weight_dish ?? false);
            // Order price includes product price and packing price
            $price = $large ? $product->price2 : $product->price;
            $packing = $large ? $product->packaging_price2 : $product->packaging_price;
            $pr_price = $price + $packing;

            $ingredients = ArrayHelper::merge(
                ['main_ingredients' => $this->getIngredients($key, $pos, 'main_ingredients')],
                ['additional_ingredients' => $this->getIngredients($key, $pos, 'additional_ingredients')]
            );
            $type = ($product instanceof Classic) ? 'classic' : 'product';
            $result[] = [
                'product_id' => $product->product_id,
                'category_id' => $product->productCategory->category_id ?? (Yii::$app->params['pizzaCategoryId'] ?? 0),
                'product_type' => $large ? 2 : 1,
                'name' => $product->productDescription->name ?? ($product->classicDescription->name ?? null),
                'weight_dish' => $product->weight_dish ?? 0,
                'quantity' => $quantity = $pos['quantity'] ?? 0,
                'price' => $pr_price,
                'total' => $quantity * $pr_price + ($ingSum = $this->getK($product, $quantity) * $this->countIngTotal($ingredients, $type)),
                'type' => $type,
                'ingredients' => json_encode($ingredients),
                'properties' => $this->getProperties($product, $pos['property_id'] ?? 0),
                'comment' => $pos['comment'] ?? null
            ];
            $this->sum += $quantity * $price + $ingSum;
            $this->packing += $quantity * $packing;
        }
        return $result;
    }

    /**
     * @param $product
     * @param $id
     * @return false|string|null
     */
    private function getProperties($product, $id)
    {
        /** @var Product $product */
        if (empty($product->properties)) return null;
        if (empty($arr = json_decode($product->properties, true))) return null;
        foreach ($arr as $item) {
            if (!empty($item['id']) && $item['id'] == $id) return json_encode($item);
        }
        return null;
    }

    /**
     * @param $product
     * @param $quantity
     * @return int
     */
    private function getK($product, $quantity): int
    {
        /** @var Product $product */
        if ($product instanceof Classic
            || $product->productCategory->category_id == Yii::$app->params['pizzaCategoryId']
            || $product->category->parent_id == Yii::$app->params['pizzaCategoryId']) {
            return $quantity;
        }
        return 1;
    }

    /**
     * @param $key
     * @param $pos
     * @return Product|array|ActiveRecord|null
     * @throws ValidationException
     */
    private function getProduct($key, $pos)
    {
        $self_made = $pos['type'] === 'classic';

        if ($self_made) {
            $product = Classic::find()->where(['product_id' => 1])->with(['productDescription'])->one();
            $mainIngCount = !empty($pos['main_ingredients']) && is_array($pos['main_ingredients']) ?
                $this->getIngCount($key, $pos, 'main_ingredients') : 0;
            $additionalIngCount = !empty($pos['additional_ingredients']) && is_array($pos['additional_ingredients']) ?
                $this->getIngCount($key, $pos, 'additional_ingredients') : 0;
            if ($mainIngCount > Yii::$app->params['maxCountMainIngredients'])
                throw new ValidationException([
                    "cart.$key.main_ingredients" => ["Ingredient count cannot be greater then " . Yii::$app->params['maxCountMainIngredients'] . '.']
                ]);
            if ($additionalIngCount > Yii::$app->params['maxCountAdditionalIngredients'])
                throw new ValidationException([
                    "cart.$key.additional_ingredients" => ["Ingredient count cannot be greater then " . Yii::$app->params['maxCountAdditionalIngredients'] . '.']
                ]);
        } else {
            if (empty($pos['product_id']))
                throw new ValidationException(["cart.$key.product_id" => [Yii::t('validation', 'This attribute is required.')]]);
            $product = Product::find()->where(['product_id' => $pos['product_id']])->with(['productCategory.category', 'productDescription'])->one();
        }

        if (empty($product)) throw new ValidationException(["cart.$key.product_id" => [Yii::t('validation', "Product not found.")]]);

        return $product;
    }

    /**
     * @param $key
     * @param array $pos
     * @param string $attr
     * @return int
     * @throws ValidationException
     */
    private function getIngCount($key, array $pos, string $attr): int
    {
        $count = 0;
        $arr = $pos[$attr];
        foreach ($arr as $k => $item) {
            if (empty($item['quantity']))
                throw new ValidationException(["cart.$key.$attr.$k.quantity" => ["This attribute is required."]]);
            $count += $item['quantity'];
        }
        return $count;
    }

    /**
     * @param $key
     * @param array $pos
     * @param string $attr
     * @return array
     * @throws ValidationException
     */
    private function getIngredients($key, array $pos, string $attr): array
    {
        if (empty($pos[$attr])) return [];
        $result = [];
        foreach ($pos[$attr] as $k => $item) {
            if (empty($item['ingredient_id']))
                throw new ValidationException([
                    "cart.$key.$attr.$k.ingredient_id" => [Yii::t('validation', "This attribute required.")]
                ]);
            if (empty($item['quantity']))
                throw new ValidationException([
                    "cart.$key.$attr.$k.quantity" => [Yii::t('validation', "This attribute required.")]
                ]);
            $ing = Ingredient::getIngredient($item['ingredient_id']);
            if (!$ing) throw new ValidationException([
                "cart.$key.$attr.$k.ingredient_id" => [Yii::t('validation', "Ingredient not found.")]
            ]);
            $result[] = array_merge($ing, ['quantity' => $item['quantity']]);
        }
        return $result;
    }

    /**
     * @param $ingredients
     * @param $type
     * @return float
     */
    private function countIngTotal($ingredients, $type)
    {
        $total = 0.0000;
        // first loop is needed because ingredients array include main and additional ingredients
        foreach ($ingredients as $key => $ingredient) {
            if ($type === 'classic' && $key === 'main_ingredients') continue;
            foreach ($ingredient as $item) {
                $total += round($item['quantity'] * $item['price'], 4);
            }
        }
        return $total;
    }

    /**
     * @param array $params
     * @return Order
     * @throws ValidationException
     * @throws NotFoundException
     * @throws InvalidConfigException
     */
    private function prepareOrder(array $params = [])
    {
        $data = Yii::$app->request->post();
        if (array_key_exists('cart', $data)) unset ($data['cart']);
        $order = new Order($data);
        if ($this->orderHasWeightDish && $order->payment_type === Order::PAYMENT_TYPE_ONLINE)
            throw new ValidationException([
                'payment_type' => [Yii::t('validation', "Payment type cannot be 'Online payment' when weight dishes are in the cart.")]
            ]);
        if (!empty($params['user'])) {
            $order->user_id = $params['user']->user_id;
            if (empty($order->email)) $order->email = $params['user']->email;
            if (empty($order->user_id)) $order->user_id = $params['user']->user_id;
        }
        $order->language_id = Language::getLanguageIdByCode(Yii::$app->language);
        $order->sum = $this->sum;
        $order->packing = $this->packing;
        $order->total = $this->sum + $this->packing;


        if (array_key_exists('from_site', $data)) {
            $order->created_with = 'site';
        } else {
            $order->created_with = 'mobile';
        }

        if (isset($order->delivery_type) && $order->delivery_type === Order::DELIVERY_TYPE_SELF_PICKING) {
            $order->delivery = 0;
            $is_action_valid = (bool)Yii::$app->params['isSelfPickingActionAvailable'];

            if ($is_action_valid) {
                $action_discount_size = (int)Yii::$app->params['selfPickingActionDiscount'];
                if (!empty($action_discount_size)) {
                    $discount = round(($order->total * ($action_discount_size / 100)), 2);
                    $order->total -= $discount;
                    $order->promotions_applied = json_encode(
                        [
                            [
                                'name' => 'Скидка при самовывозе',
                                'discount_size' => $action_discount_size . '%',
                                'discount_sum' => $discount
                            ]
                        ]
                    );
                }
            }
        } else {
            $order->delivery = $this->getOrderDelivery($order);
            $order->total += $order->delivery;
        }

        $order->scenario = 'create';
        if ($order->validate()) return $order;

        throw new ValidationException($order->getErrors());
    }

    /**
     * @param Order $order
     * @return int|null
     * @throws ValidationException
     * @throws NotFoundException
     */
    private function getOrderDelivery(Order $order)
    {
//        if ($this->orderHasWeightDish) return 0;
        if ($order->city_id == Yii::$app->params['orderDefaultCityId'] ?? 0) {
            $minFreeDelPrice = Yii::$app->params['minKovelFreeDeliveryPrice'] ?? -1;
            if ($minFreeDelPrice > 0 && (float)$order->total >= (float)$minFreeDelPrice) return 0;
        }
        if (empty($order->city_id))
            throw new ValidationException(['city_id' => [Yii::t('validation', 'This attribute is required.')]]);
        $city = City::findOne(['id' => $order->city_id]);
        if (!$city) throw new NotFoundException(Yii::t('notFound', 'City is not found.'));

        if ((float)$order->total > (float)$city->free_minimum_order) {
            return 0;
        }
        return $city->delivery_price;
    }

    /**
     * @param array $products
     * @return float|mixed
     */
    private function countProdTotal(array $products)
    {
        $total = 0.0000;
        foreach ($products as $product) {
            $total += $product['total'];
        }
        return $total;
    }

    /**
     * @param Order $order
     */
    private function saveToHistory(Order $order)
    {
        // Add new record to order history
        $orderHistoryModel = new OrderHistory();
        $orderHistoryModel->order_id = $order->order_id;
        $orderHistoryModel->status = $order->status;
        $orderHistoryModel->comment = '';
        $orderHistoryModel->save(false);
    }

    /**
     * @param Order $order
     */
    private function print(Order $order)
    {
        $text = $this->renderPartial('@app/printer/order-text', [
            'order' => $order,
            'orderProducts' => $order->orderProducts,
        ]);


        Yii::$app->queue->push(new PrintOrderJob([
            'order_id' => $order->order_id,
            'text' => base64_encode($text)
        ]));
    }

    /**
     * @param Order $order
     */
    private function broadcastOnViber(Order $order)
    {
        ViberSender::broadcastMessage([
            'text' => 'Вам прийшло нове замовлення №' . $order->order_id . ".\n\n" . OrderInfoTrait::getOrderInfoMessage($order)
        ]);
        try {
            sleep(2);
            $chunks = $order->getOrderProducts()->batch(6);
            foreach ($chunks as $products) {
                if (!empty($arr = OrderInfoTrait::getProductsList($products, $order->currency_code)))
                    ViberSender::broadcastMessage([
                        'rich_media' => $arr
                    ], 1,
                        ['min_api_version' => 2, 'tracking_data' => 'message']);
            }
        } catch (\Throwable $e) {
            Helper::log([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'viber');
        }
    }
}
