<?php

namespace app\module\admin\module\order\models;

use app\module\admin\models\Restaurant;
use app\module\admin\models\SettingForm;
use app\module\admin\module\currency\models\Currency;
use app\module\admin\module\pizzeria\models\Pizzeria;
use app\module\api\module\viber\controllers\helpers\T;
use Carbon\Carbon;
use Exception;
use Yii;
use app\module\admin\models\Language;
use app\module\admin\models\User;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * @property int $order_id
 * @property int $user_id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property int $city_id
 * @property string $street
 * @property string $entrance
 * @property string $created_with
 * @property string $house_number
 * @property string $apartment_number
 * @property int $do_not_call
 * @property int $call_me_back
 * @property int $have_a_child
 * @property int $have_a_dog
 * @property string $comment
 * @property string $promotions_applied
 * @property int $time
 * @property int $payment_type
 * @property int $delivery_type
 * @property int $payment_status
 * @property int $language_id
 * @property int $currency_id
 * @property string $currency_code
 * @property string $currency_value
 * @property float $sum
 * @property float $packing
 * @property float $delivery
 * @property float $total
 * @property float $total_for_online_payment
 * @property int $pizzeria_id
 * @property int $restaurant_id
 * @property int $rating
 * @property int $status
 * @property int $created_at
 * @property int $is_deleted
 * @property int $updated_at
 *
 * @property array $orderProducts
 * @property Pizzeria $pizzeria
 * @property Restaurant $restaurant
 * @property City $city
 * @property User $user
 */
class Order extends ActiveRecord
{
    public $test;
    public const STATUS_PENDING = 1;
    public const STATUS_ORDER_BEING_PREPARED = 2;
    public const STATUS_ORDER_ON_ROAD = 3;
    public const STATUS_ORDER_DELIVERED = 4;
    public const STATUS_ORDER_CANCELED = 5;

    public const PAYMENT_TYPE_IN_CASH = 1;
    public const PAYMENT_TYPE_ONLINE = 2;
    public const PAYMENT_TERMINAL = 3;

    const DELIVERY_TYPE_ADDRESS = 2;
    const DELIVERY_TYPE_SELF_PICKING = 1;

    public const PAYMENT_STATUS_NOT_PAID = 0;
    public const PAYMENT_STATUS_PAID = 1;

    public const NO = 0;
    public const YES = 1;

    /**
     * Order constructor.
     * @param array $config
     * @throws InvalidConfigException
     */
    public function __construct($config = [])
    {
        $keys = array_keys(static::getTableSchema()->columns);
        $diff = array_diff(array_keys($config), $keys);
        if (!empty($diff)) {
            foreach ($diff as $key) {
                unset ($config[$key]);
            }
        }
        parent::__construct($config);
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'tbl_order';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $currency = Currency::getDefault();
        return [
            ['payment_type', 'default', 'value' => self::PAYMENT_TYPE_IN_CASH],
            ['payment_status', 'default', 'value' => self::PAYMENT_STATUS_NOT_PAID],
            ['currency_id', 'default', 'value' => $currency->currency_id ?? null],
            ['currency_code', 'default', 'value' => $currency->code ?? null],
            ['currency_value', 'default', 'value' => $currency->value ?? null],
            ['status', 'default', 'value' => self::STATUS_PENDING],

            [
                [
                    'phone',
                    'sum',
                    'packing',
                    'delivery',
                    'total',
                    'language_id',
                    'currency_id',
                    'currency_code',
                    'currency_value',
                    'payment_type',
                    'payment_status',
                    'status',
                    'time',
                ],
                'required'
            ],
            [
                [
                    'user_id',
                    'payment_type',
                    'delivery_type',
                    'payment_status',
                    'status',
                    'language_id',
                    'currency_id',
                    'pizzeria_id',
                    'restaurant_id',
                    'city_id',
                    'rating',
                    'do_not_call',
                    'have_a_child',
                    'have_a_dog',
                    'call_me_back',
                    'created_at',
                    'updated_at',
                    'is_deleted'
                ],
                'integer'
            ],
            [['comment', 'promotions_applied'], 'string'],
            [['packing', 'delivery', 'sum', 'total', 'total_for_online_payment', 'currency_value'], 'number'],
            [['name', 'email', 'street', 'entrance', 'created_with', 'house_number', 'apartment_number'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 32],
            [['currency_code'], 'string', 'max' => 3],
            ['time', 'validateTime', 'on' => 'create'],
            [
                'payment_type',
                'in',
                'range' => [
                    self::PAYMENT_TYPE_IN_CASH,
                    self::PAYMENT_TYPE_ONLINE,
                    self::PAYMENT_TERMINAL
                ]
            ],
            [
                'delivery_type',
                'in',
                'range' => [
                    self::DELIVERY_TYPE_ADDRESS,
                    self::DELIVERY_TYPE_SELF_PICKING
                ]
            ],
            [
                'payment_status',
                'in',
                'range' => [
                    self::PAYMENT_STATUS_NOT_PAID,
                    self::PAYMENT_STATUS_PAID
                ]
            ],
            [
                'status',
                'in',
                'range' => [
                    self::STATUS_PENDING,
                    self::STATUS_ORDER_BEING_PREPARED,
                    self::STATUS_ORDER_ON_ROAD,
                    self::STATUS_ORDER_DELIVERED,
                    self::STATUS_ORDER_CANCELED
                ]
            ],
            [['do_not_call', 'have_a_child', 'have_a_dog', 'call_me_back'], 'default', 'value' => self::NO],
            [['do_not_call', 'have_a_child', 'have_a_dog', 'call_me_back'], 'in', 'range' => [
                self::NO,
                self::YES
            ]],
            [['rating'], 'default', 'value' => 0],
            [
                'rating',
                'in',
                'range' => [0, 1, 2, 3, 4, 5]
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'order_id' => '№ заказа',
            'user_id' => 'Пользователь',
            'name' => 'Имя пользователя',
            'email' => 'Email',
            'phone' => 'Номер телефона',
            'street' => 'Улица',
            'entrance' => 'Подъезд',
            'house_number' => '№ дома',
            'apartment_number' => '№ квартиры',
            'comment' => 'Пожелания клиента к заказу',
            'packing' => 'Упаковка',
            'delivery' => 'Доставка',
            'sum' => 'Сумма',
            'total' => 'Итого',
            'total_for_online_payment' => 'Итого (без весовых блюд)',
            'payment_type' => 'Способ оплаты',
            'delivery_type' => 'Тип доставки',
            'payment_status' => 'Статус оплаты',
            'status' => 'Статус заказа',
            'language_id' => 'Язык',
            'currency_id' => 'Валюта',
            'currency_code' => 'Код валюты',
            'created_with' => 'Создано через',
            'currency_value' => 'Значение валюты',
            'time' => 'Время доставки',
            'pizzeria_id' => 'Пиццерия',
            'restaurant_id' => 'Заведение (Ресторан)',
            'city_id' => 'Населенный пункт',
            'rating' => 'Оценка пользователя',
            'do_not_call' => 'Не звонить клиенту в дверь',
            'call_me_back' => 'Передзвонить мне',
            'have_a_child' => 'У клиента есть маленький ребенок',
            'have_a_dog' => 'У клиента есть собака',
            'created_at' => 'Создано',
            'is_deleted' => 'Удалено',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPizzeria(): ActiveQuery
    {
        return $this->hasOne(Pizzeria::class, ['pizzeria_id' => 'pizzeria_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRestaurant(): ActiveQuery
    {
        return $this->hasOne(Restaurant::class, ['restaurant_id' => 'restaurant_id']);
    }


    /**
     * @return mixed
     */
    public function getRestaurantName()
    {
        return ($this->restaurant instanceof Restaurant) ? $this->restaurant->restaurantName : 'Не указано';
    }



    /**
     * @return mixed
     */
    public function getPizzeriaName()
    {
        return ($this->pizzeria instanceof Pizzeria) ? $this->pizzeria->getPizzeriaName() : 'Не указано';
    }

    /**
     * @return float
     */
    public function getTotalSum(): float
    {
        return $this->sum + $this->packing + $this->delivery;
    }

    /**
     * Updates order total value.
     */
    public function updateTotal()
    {
        $sum = 0.0;

        $orderProducts = OrderProduct::findAll(['order_id' => $this->order_id]);

        foreach ($orderProducts as $orderProduct) {
            $sum += $orderProduct->price * $orderProduct->quantity;
        }

        $this->sum = $sum;
        $this->total = $sum;

        $this->save(false);
    }

    /**
     * Updates order status.
     */
    public function updateStatus()
    {
        $status = (new Query())
            ->select('status')
            ->from(OrderHistory::tableName())
            ->where(['order_id' => $this->order_id])
            ->orderBy('created_at DESC')
            ->scalar();

        $this->status = !empty($status) ? $status : Order::STATUS_PENDING;

        if ($this->save(false) && $this->status != Order::STATUS_PENDING) {
            $languageCode = Language::getLanguageCodeById($this->language_id);
            $message = [
                'text' => T::t('order', 'Статус замовлення змінено на:', [],
                        $languageCode) . ' ' . self::getStatusName($this->status, $languageCode),
                'status' => $this->status,
                'status_text' => self::getStatusName($this->status, $languageCode),
                'restaurant' => $this->getPizzeriaName()
            ];
            $header = T::t('order', 'Замовлення номер') . " $this->order_id";
//            $message = T::t('order', 'Статус замовлення змінено на:', [],
//                    $languageCode) . ' ' . self::getStatusName($this->status, $languageCode);

            if (!empty($this->user->device_id)) {
//                User::sendExpoNotification('Classic', $message, $this->user->device_id);
                User::sendExpoNotification($header, $message['text'], $this->user->device_id);
            }

//            User::addToNotificationsHistory('Classic', $message, [$this->user_id]);
            User::addToNotificationsHistory($header, $message, [$this->user_id]);
        }
    }

    /**
     * Returns products count in order.
     */
    public function getProductsCount()
    {
        $count = 0;

        /** @var OrderProduct $product */
        foreach (OrderProduct::find()->where(['order_id' => $this->order_id])->all() as $product) {
            $count += $product->quantity;
        }

        return $count;
    }

    /**
     * ActiveRelation to OrderProduct model.
     *
     * @return ActiveQuery active query instance
     */
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProduct::class, ['order_id' => 'order_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['user_id' => 'user_id']);
    }

    /**
     * @return mixed
     */
    public function getCityName()
    {
        if ($this->city_id !== null) {
            return $this->city->getCityName();
        }
        return null;
    }

    /**
     * Sends email to admin about new order.
     *
     * @param array $orderProducts order product models
     * @return bool whether email was send successfully
     */
    public function sendAdminEmail($orderProducts)
    {
        $siteName = isset(Yii::$app->params['siteName']) ? Yii::$app->params['siteName'] : Yii::$app->name;

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => '@app/mail/frontend/adminNewOrder-html', 'text' => '@app/mail/frontend/adminNewOrder-text'],
                [
                    'order' => $this,
                    'orderProducts' => $orderProducts,
                ]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => $siteName . ' робот'])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject('Новый заказ № ' . $this->order_id)
            ->send();
    }

    /**
     * @param bool $cartHasWeightDishProduct
     */
    public function calculateDeliveryPrice($cartHasWeightDishProduct): void
    {
        $minKovelFreeDeliveryPrice = !empty(Yii::$app->params['minKovelFreeDeliveryPrice']) ? (float) Yii::$app->params['minKovelFreeDeliveryPrice'] : 10000.0000;
        $orderDefaultCityId = !empty(Yii::$app->params['orderDefaultCityId']) ? (int) Yii::$app->params['orderDefaultCityId'] : 0;

        $cityDeliveryPrice = 0.0000;

        if ($this->city_id) {
            /** @var City $orderCity */
            $orderCity = City::findOne($this->city_id);

            if ($orderCity) {
                $cityDeliveryPrice = $orderCity->delivery_price;
            }
        }

        if ((($this->sum + $this->packing) > $minKovelFreeDeliveryPrice) && $this->city_id && (int)$this->city_id === $orderDefaultCityId) {
            $this->delivery = 0.0000;
        } else {
            $this->delivery = $cityDeliveryPrice;
        }


//        if ($cartHasWeightDishProduct) {
//            $this->delivery = 0.0000;
//        } elseif ((($this->sum + $this->packing) > $minKovelFreeDeliveryPrice) && $this->city_id && (int)$this->city_id === $orderDefaultCityId) {
//            $this->delivery = 0.0000;
//        } else {
//            $this->delivery = $cityDeliveryPrice;
//        }
    }

    /**
     * Returns payment type name by specified payment type constant.
     *
     * @param integer $type payment type constant
     * @return mixed|string payment type name
     */
    public static function getPaymentTypeName($type)
    {
        $paymentTypes = self::getPaymentTypesList();
        return isset($paymentTypes[$type]) ? $paymentTypes[$type] : 'Неопределено';
    }


    public static function getDeliveryTypeName($type)
    {
        $deliveryTypes = self::getDeliveryTypesList();
        return isset($deliveryTypes[$type]) ? $deliveryTypes[$type] : 'Неопределено';
    }




    /**
     * Returns payment types list.
     *
     * @return array payment types list data
     */
    public static function getPaymentTypesList()
    {
        return [
            self::PAYMENT_TYPE_IN_CASH => T::t('order', 'Готівкою при отриманні'),
            self::PAYMENT_TYPE_ONLINE => T::t('order', 'Онлайн оплата'),
            self::PAYMENT_TERMINAL => T::t('order', 'Терміналом')
        ];
    }


    public static function getDeliveryTypesList()
    {
        return [
            self::DELIVERY_TYPE_ADDRESS => T::t('order', 'Доставка за адресою'),
            self::DELIVERY_TYPE_SELF_PICKING => T::t('order', 'Самовивіз')
        ];
    }

    /**
     * Returns payment status name by specified payment status constant.
     *
     * @param integer $type payment status constant
     * @return mixed|string payment status name
     */
    public static function getPaymentStatusName($type)
    {
        $paymentStatuses = self::getPaymentStatusesList();
        return isset($paymentStatuses[$type]) ? $paymentStatuses[$type] : 'Неопределено';
    }

    /**
     * Returns payment statuses list.
     *
     * @return array payment statuses list data
     */
    public static function getPaymentStatusesList()
    {
        return [
            self::PAYMENT_STATUS_NOT_PAID => T::t('order', 'Не оплачено'),
            self::PAYMENT_STATUS_PAID => T::t('order', 'Оплачено')
        ];
    }

    /**
     * Returns statuses list.
     *
     * @param string|null $lang language code
     * @return array statuses list data
     */
    public static function getStatusesList($lang = null)
    {
        return [
            self::STATUS_PENDING => T::t('order', 'В очікуванні', [], $lang),
            self::STATUS_ORDER_BEING_PREPARED => T::t('order', 'Замовлення готується', [], $lang),
            self::STATUS_ORDER_ON_ROAD => T::t('order', 'Замовлення в дорозі', [], $lang),
            self::STATUS_ORDER_DELIVERED => T::t('order', 'Доставлено', [], $lang),
            self::STATUS_ORDER_CANCELED => T::t('order', 'Відмінено', [], $lang)
        ];
    }

    /**
     * Returns status name by specified status constant.
     *
     * @param integer $status status constant
     * @param string|null $lang language code
     * @return mixed|string status name
     */
    public static function getStatusName($status, $lang = null)
    {
        $statuses = self::getStatusesList($lang);
        return isset($statuses[$status]) ? $statuses[$status] : 'Неопределено';
    }

    /**
     * Returns all models count.
     *
     * @return int|string models count
     */
    public static function getAllCount()
    {
        return self::find()->count();
    }

    /**
     * Checks if user has one created order.
     *
     * @param int $userId user
     * @return bool true if user has one created order, false - otherwise
     */
    public static function checkFirstOrder($userId)
    {
        return ((int)self::find()->where(['user_id' => $userId])->count() === 1);
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     * @throws Exception
     */
    public function validateTime($attribute, $params)
    {
        $time = Carbon::createFromTimestamp($this->$attribute)->timezone('Europe/Kyiv');
        list($start, $end) = $this->delivery_type === static::DELIVERY_TYPE_ADDRESS
            ? SettingForm::getDeliveryTime()
            : SettingForm::getSelfPickingTime();

        $today = Carbon::now('Europe/Kyiv')->format('d.m.Y');

        if ($today != $time->format('d.m.Y')) {
            $this->addError($attribute, T::t('validation', 'Оформити замовлення можливо лише на сьогодні.'));

            return;
        }

        if (!empty($start) && $start > $time->format('H:i')) {
            $this->addError($attribute, T::t('validation', 'Можливий час доставки / самовивозу замовлення з {from} за Київським часом.', [
                'from' => $start,
            ]));

            return;
        }

        if (!empty($end) && $time->format('H:i') > $end) {
            $this->addError($attribute, T::t('validation', 'Можливий час доставки / самовивозу замовлення до {to} за Київським часом.', [
                'to' => $end,
            ]));
            return;
        }

        if ($this->delivery_type === static::DELIVERY_TYPE_ADDRESS) {
            $time = Carbon::createFromTimestamp($this->$attribute);
            if (!empty(Yii::$app->params['deliveryDuration'])) {
                $minTime = Carbon::now()->addMinutes((int) (preg_replace('/[^0-9]/', '', Yii::$app->params['deliveryDuration'])));
                $minTime = Carbon::today('GMT+3')->addHours($minTime->format('H'))->addMinutes($minTime->format('m'));
                if ($minTime->diffInMinutes($time, false) < 0) {
                    $this->addError($attribute, T::t('validation', "Time cannot be earlier than today ") . $minTime->toTimeString());
                    return;
                }
            }
        }
    }
}
