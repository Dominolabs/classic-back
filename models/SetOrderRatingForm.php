<?php

namespace app\models;

use app\module\admin\module\order\models\Order;
use Yii;
use yii\base\Model;

/**
 * @property int $order_id
 * @property int $rating
 */
class SetOrderRatingForm extends Model
{
    /**
     * @var integer
     */
    public $order_id;

    /**
     * @var integer
     */
    public $rating;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['order_id', 'rating'], 'integer'],
            ['order_id', 'exist', 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'order_id']],
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
            'rating' => Yii::t('api', 'Оцінка користувача'),
        ];
    }
}
