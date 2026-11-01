<?php

namespace tests\models;

use app\module\admin\module\order\models\Order;
use Carbon\Carbon;
use Yii;
use yii\helpers\FileHelper;

/**
 * Order without a DB connection: Order::__construct() and ActiveRecord::attributes()
 * both read the table schema, which validateTime() does not need.
 */
class OrderWithoutDb extends Order
{
    public function __construct()
    {
    }

    public function attributes()
    {
        return ['time', 'delivery_type'];
    }
}

class OrderValidateTimeTest extends \Codeception\Test\Unit
{
    private const TZ = 'Europe/Kyiv';

    private $params;

    protected function _before()
    {
        $this->params = Yii::$app->params;

        // No working hours configured -> SettingForm::getDeliveryTime()/getSelfPickingTime() return ['', '']
        Yii::$app->params['deliveryTime'] = [];
        Yii::$app->params['selfPickingTime'] = [];

        // Each case sets only the minimums it needs
        unset(
            Yii::$app->params['minCookingTime'],
            Yii::$app->params['minCookingTimeSelfPickup'],
            Yii::$app->params['deliveryDuration']
        );

        // T::t() falls back to the raw message and logs to runtime/logs when the translations DB is unreachable
        FileHelper::createDirectory(Yii::getAlias('@app/runtime/logs'));
    }

    protected function _after()
    {
        Carbon::setTestNow();
        Yii::$app->params = $this->params;
    }

    /**
     * @dataProvider minimumTimeProvider
     *
     * @param string $now Kyiv time, 'Y-m-d H:i[:s]'
     * @param int $deliveryType Order::DELIVERY_TYPE_*
     * @param array $params merged into Yii::$app->params
     * @param string $chosen Kyiv time chosen by the customer, 'Y-m-d H:i'
     * @param string|null $minTimeInMessage 'H:i' expected in the error, null when the time must be accepted
     */
    public function testMinimumTime($now, $deliveryType, array $params, $chosen, $minTimeInMessage)
    {
        Carbon::setTestNow(Carbon::parse($now, self::TZ));
        Yii::$app->params = array_merge(Yii::$app->params, $params);

        $order = new OrderWithoutDb();
        $order->delivery_type = $deliveryType;
        $order->time = Carbon::parse($chosen, self::TZ)->getTimestamp();

        $order->validateTime('time', []);

        if ($minTimeInMessage === null) {
            $this->assertFalse($order->hasErrors('time'), "$chosen must be accepted at $now");

            return;
        }

        $this->assertTrue($order->hasErrors('time'), "$chosen must be rejected at $now");
        $this->assertTrue(
            strpos($order->getFirstError('time'), $minTimeInMessage) !== false,
            "Error must mention $minTimeInMessage, got: " . $order->getFirstError('time')
        );
    }

    public function minimumTimeProvider()
    {
        $summer = '2026-08-15';
        $winter = '2026-11-16';
        $address = Order::DELIVERY_TYPE_ADDRESS;
        $pickup = Order::DELIVERY_TYPE_SELF_PICKING;

        return [
            // delivery by address: not earlier than now + 1 min, in both UTC+3 and UTC+2
            'summer: current minute' => ["$summer 10:02", $address, [], "$summer 10:02", '10:03'],
            'summer: next minute' => ["$summer 10:02", $address, [], "$summer 10:03", null],
            'summer: reported 10:55' => ["$summer 10:02", $address, [], "$summer 10:55", null],
            'winter: current minute' => ["$winter 10:02", $address, [], "$winter 10:02", '10:03'],
            'winter: next minute' => ["$winter 10:02", $address, [], "$winter 10:03", null],
            'winter: reported 10:55' => ["$winter 10:02", $address, [], "$winter 10:55", null],

            // seconds of "now" are dropped, so the next minute is accepted
            'seconds: next minute' => ["$summer 10:02:30", $address, [], "$summer 10:03", null],
            'seconds: current minute' => ["$summer 10:02:30", $address, [], "$summer 10:02", '10:03'],

            // minimum cooking time is enforced by the clients, not by the server
            'minCookingTime is ignored' => ["$summer 10:02", $address, ['minCookingTime' => 45], "$summer 10:10", null],
            'deliveryDuration is ignored' => ["$summer 10:02", $address, ['deliveryDuration' => [1 => '45 хв', 2 => '45 min']], "$summer 10:10", null],

            // self picking has no minimum
            'self picking: no minimum' => ["$summer 10:02", $pickup, ['minCookingTimeSelfPickup' => 15], "$summer 10:03", null],
        ];
    }
}
