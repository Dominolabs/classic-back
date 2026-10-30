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
        $cooking45 = ['minCookingTime' => 45];

        return [
            // now 10:02 + 45 min -> minimum 10:47 Kyiv, in both UTC+3 and UTC+2
            'summer: before minimum' => ["$summer 10:02", $address, $cooking45, "$summer 10:46", '10:47'],
            'summer: exactly minimum' => ["$summer 10:02", $address, $cooking45, "$summer 10:47", null],
            'summer: reported 10:55' => ["$summer 10:02", $address, $cooking45, "$summer 10:55", null],
            'winter: before minimum' => ["$winter 10:02", $address, $cooking45, "$winter 10:46", '10:47'],
            'winter: exactly minimum' => ["$winter 10:02", $address, $cooking45, "$winter 10:47", null],
            'winter: reported 10:55' => ["$winter 10:02", $address, $cooking45, "$winter 10:55", null],

            // now 10:50 + 45 min -> minimum 11:35 (crosses the hour)
            'hour rollover: before minimum' => ["$summer 10:50", $address, $cooking45, "$summer 11:34", '11:35'],
            'hour rollover: exactly minimum' => ["$summer 10:50", $address, $cooking45, "$summer 11:35", null],

            // the configured value is used, not a fixed 1 minute
            'value is honoured' => ["$summer 10:02", $address, ['minCookingTime' => 120], "$summer 11:00", '12:02'],

            // seconds of "now" are dropped, so the minimum the customer was shown is accepted
            'seconds: exactly minimum' => ["$summer 10:02:30", $address, $cooking45, "$summer 10:47", null],
            'seconds: before minimum' => ["$summer 10:02:30", $address, $cooking45, "$summer 10:46", '10:47'],

            // shapes of the setting as saved by SettingForm
            'string value' => ["$summer 10:02", $address, ['minCookingTime' => '45'], "$summer 10:46", '10:47'],
            'empty value is skipped' => ["$summer 10:02", $address, ['minCookingTime' => ''], "$summer 10:03", null],
            'zero is skipped' => ["$summer 10:02", $address, ['minCookingTime' => 0], "$summer 10:03", null],
            'missing setting is skipped' => ["$summer 10:02", $address, [], "$summer 10:03", null],

            // deliveryDuration is display text and does not affect validation
            'deliveryDuration is ignored' => [
                "$summer 10:02",
                $address,
                ['minCookingTime' => 30, 'deliveryDuration' => [1 => '45-60 хв', 2 => '45-60 min']],
                "$summer 10:32",
                null,
            ],

            // self picking uses its own setting
            'self picking: before minimum' => ["$summer 10:02", $pickup, ['minCookingTimeSelfPickup' => 15], "$summer 10:16", '10:17'],
            'self picking: exactly minimum' => ["$summer 10:02", $pickup, ['minCookingTimeSelfPickup' => 15], "$summer 10:17", null],
            'self picking ignores minCookingTime' => ["$summer 10:02", $pickup, $cooking45, "$summer 10:10", null],
            'address ignores minCookingTimeSelfPickup' => ["$summer 10:02", $address, ['minCookingTimeSelfPickup' => 45], "$summer 10:10", null],
        ];
    }
}
