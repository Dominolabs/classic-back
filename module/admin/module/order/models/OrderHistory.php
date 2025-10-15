<?php

namespace app\module\admin\module\order\models;

use app\module\admin\models\Restaurant;
use app\module\admin\module\pizzeria\models\Pizzeria;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * @property int $order_history_id
 * @property int $order_id
 * @property int $pizzeria_id
 * @property int $restaurant_id
 * @property int $status
 * @property string $comment
 * @property int $created_at
 *
 * @property Pizzeria $pizzeria
 * @property Restaurant $restaurant
 */
class OrderHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_order_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'status'], 'required'],
            [['order_id', 'pizzeria_id', 'restaurant_id', 'created_at'], 'integer'],
            [['comment'], 'string'],
            ['status', 'default', 'value' => Order::STATUS_PENDING],
            ['status', 'in', 'range' => [
                Order::STATUS_PENDING,
                Order::STATUS_ORDER_BEING_PREPARED,
                Order::STATUS_ORDER_ON_ROAD,
                Order::STATUS_ORDER_DELIVERED,
                Order::STATUS_ORDER_CANCELED
            ]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_history_id' => 'ID записи истории заказов',
            'order_id' => '№ заказа',
            'pizzeria_id' => 'Пиццерия',
            'restaurant_id' => 'Заведение (Ресторан)',
            'status' => 'Статус',
            'comment' => 'Коментарий',
            'created_at' => 'Создано',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp'  => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ]
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPizzeria()
    {
        return $this->hasOne(Pizzeria::class, ['pizzeria_id' => 'pizzeria_id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getRestaurant()
    {
        return $this->hasOne(Restaurant::class, ['restaurant_id' => 'restaurant_id']);
    }



    /**
     * @return mixed
     */
    public function getPizzeriaName()
    {
        return ($this->pizzeria instanceof Pizzeria) ? $this->pizzeria->getPizzeriaName() : 'Не указано';
    }



    /**
     * @return mixed
     */
    public function getRestaurantName()
    {
        return ($this->restaurant instanceof Restaurant) ? $this->restaurant->getRestaurantTitleWithAddress() : 'Не указано';
    }


    /**
     * Removes order history records by order id.
     *
     * @param string $orderId order id
     */
    public static function removeByOrderId($orderId)
    {
        self::deleteAll(['order_id' => $orderId]);
    }
}
