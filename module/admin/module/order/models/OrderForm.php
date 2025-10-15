<?php

namespace app\module\admin\module\order\models;

use app\module\admin\models\User;
use Carbon\Carbon;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;

/**
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
 * @property int $payment_type
 */
class OrderForm extends Model
{
    public $name;
    public $email;
    public $phone;
    public $city_id;
    public $created_with;
    public $street;
    public $entrance;
    public $house_number;
    public $apartment_number;
    public $do_not_call = 0;
    public $call_me_back = 0;
    public $have_a_child = 0;
    public $have_a_dog = 0;
    public $comment;
    public $time;
    public $payment_type;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'phone', 'time', 'payment_type'], 'required'],
            [['name', 'email', 'street', 'entrance', 'created_with', 'house_number', 'apartment_number'], 'string', 'max' => 255],
            ['email', 'email'],
            ['phone', 'string', 'max' => 32],
            ['phone', 'string', 'min' => 7],
            [['comment'], 'string', 'max' => 10000],
            [['payment_type', 'city_id', 'do_not_call', 'have_a_child', 'have_a_dog', 'call_me_back'], 'integer'],
            ['payment_type', 'default', 'value' => Order::PAYMENT_TYPE_IN_CASH],
            ['payment_type', 'in', 'range' => [
                Order::PAYMENT_TYPE_IN_CASH,
                Order::PAYMENT_TYPE_ONLINE
            ]],
            [['do_not_call', 'have_a_child', 'call_me_back', 'have_a_dog'], 'default', 'value' => Order::NO],
            [['do_not_call', 'have_a_child', 'call_me_back', 'have_a_dog'], 'in', 'range' => [
                Order::NO,
                Order::YES
            ]],
            [['time'], 'validateTime']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('api', 'Ім\'я'),
            'email' => Yii::t('api', 'E-mail'),
            'phone' => Yii::t('api', 'Телефон'),
            'city_id' => Yii::t('api', 'Населений пункт'),
            'street' => Yii::t('api', 'Вулиця'),
            'entrance' => Yii::t('api', 'Під\'їзд'),
            'house_number' => Yii::t('api', '№ квартири'),
            'apartment_number' => Yii::t('api', '№ будинку'),
            'do_not_call' => Yii::t('api', 'Не дзвонити в двері, спить дитина'),
            'call_me_back' => Yii::t('api', 'Перетелефонувати мені'),
            'have_a_child' => Yii::t('api', 'У мене є маленька дитина'),
            'have_a_dog' => Yii::t('api', 'У мене є собака'),
            'comment' => Yii::t('api', 'Ваші побажання до замовлення'),
            'time' => Yii::t('api', 'Час доставки'),
            'payment_type' => Yii::t('api', 'Спосіб оплати'),
        ];
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function validateTime($attribute, $params): void
    {
        $time = Carbon::createFromTimestamp($this->$attribute);
        if (Carbon::today('GMT+3')->diffInDays($time) > 0) {
            $this->addError($attribute, Yii::t('order', 'На даний момент замовлення можна робити лише на сьогодні.'));
            return;
        }
        if (!empty(Yii::$app->params['minDeliveryTime'])) {
            $minTime = preg_replace('/[^0-9]/', '', Yii::$app->params['deliveryDuration'][1] ?? '45');
            $minTime = Carbon::now('GMT+3')->addMinutes((int) $minTime);
            if ($minTime->diffInMinutes($time, false) < 0) {
                $this->addError($attribute, Yii::t('order', 'Час доставки / приготування замовлення не може бути меншим ніж {mintime} за Київським часом.', [
                    'mintime' => (((int) $minTime->toTimeString()))
                ]));
                return;
            }
        }
        $time = Carbon::today()->addHours((int) $time->format('H'))->addMinutes((int) $time->format('i'));


        $deliveryTime = preg_replace('/[^0-9:]/', '', Yii::$app->params['deliveryTime'][1]);
        if (strlen($deliveryTime) !== 10) return;
        $start = explode(':', substr($deliveryTime, 0, 5));
        $end = explode(':', substr($deliveryTime, 5, 5));


        $start = Carbon::today('GMT+3')->addHours((int)($start[0] ?? 0))->addMinutes((int)($start[1] ?? 0));
        $end = Carbon::today('GMT+3')->addHours((int)($end[0] ?? 0))->addMinutes((int)($end[1] ?? 0));

        if ($time->diffInMinutes($end, false) < 0 || $start->diffInMinutes($time, false) < 0) {
            $this->addError($attribute, Yii::t('order', 'Можливий час доставки / самовивозу замовлення з {from} до {to} за Київським часом.', [
                'from' => $start->toTimeString(),
                'to' => $end->toTimeString(),
            ]));
        }
    }
}
