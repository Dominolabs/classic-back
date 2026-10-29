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

        // T::t() falls back to the raw message and logs to runtime/logs when the translations DB is unreachable
        FileHelper::createDirectory(Yii::getAlias('@app/runtime/logs'));
    }

    protected function _after()
    {
        Carbon::setTestNow();
        Yii::$app->params = $this->params;
    }

    /**
     * @dataProvider deliveryProvider
     *
     * @param string $now Kyiv time, 'Y-m-d H:i'
     * @param mixed $duration value of params['deliveryDuration']
     * @param string $chosen Kyiv time chosen by the customer, 'Y-m-d H:i'
     * @param string|null $minTimeInMessage 'H:i' expected in the error, null when the time must be accepted
     */
    public function testDeliveryByAddress($now, $duration, $chosen, $minTimeInMessage)
    {
        Carbon::setTestNow(Carbon::parse($now, self::TZ));
        Yii::$app->params['deliveryDuration'] = $duration;

        $order = new OrderWithoutDb();
        $order->delivery_type = Order::DELIVERY_TYPE_ADDRESS;
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

    public function deliveryProvider()
    {
        $summer = '2026-08-15';
        $winter = '2026-11-16';
        $uk45 = [1 => '45 хв', 2 => '45 min'];

        return [
            // now 10:02 + 45 min -> minimum 10:47 Kyiv, in both UTC+3 and UTC+2
            'summer: before minimum' => ["$summer 10:02", $uk45, "$summer 10:46", '10:47'],
            'summer: exactly minimum' => ["$summer 10:02", $uk45, "$summer 10:47", null],
            'summer: reported 10:55' => ["$summer 10:02", $uk45, "$summer 10:55", null],
            'winter: before minimum' => ["$winter 10:02", $uk45, "$winter 10:46", '10:47'],
            'winter: exactly minimum' => ["$winter 10:02", $uk45, "$winter 10:47", null],
            'winter: reported 10:55' => ["$winter 10:02", $uk45, "$winter 10:55", null],

            // now 10:50 + 45 min -> minimum 11:35 (crosses the hour)
            'hour rollover: before minimum' => ["$summer 10:50", $uk45, "$summer 11:34", '11:35'],
            'hour rollover: exactly minimum' => ["$summer 10:50", $uk45, "$summer 11:35", null],

            // the configured duration is used, not a fixed 1 minute
            'duration is honoured' => ["$summer 10:02", [1 => '120 хв'], "$summer 11:00", '12:02'],

            // shapes of params['deliveryDuration']
            'scalar duration' => ["$summer 10:02", '45', "$summer 10:46", '10:47'],
            'only second language key' => ["$summer 10:02", [2 => '45 min'], "$summer 10:46", '10:47'],
            'empty duration is skipped' => ["$summer 10:02", [], "$summer 10:03", null],
            'missing digits are skipped' => ["$summer 10:02", [1 => 'швидко'], "$summer 10:03", null],
            'zero duration is skipped' => ["$summer 10:02", [1 => '0'], "$summer 10:03", null],
        ];
    }

    public function testSelfPickingIgnoresDeliveryDuration()
    {
        Carbon::setTestNow(Carbon::parse('2026-08-15 10:02', self::TZ));
        Yii::$app->params['deliveryDuration'] = [1 => '45 хв'];

        $order = new OrderWithoutDb();
        $order->delivery_type = Order::DELIVERY_TYPE_SELF_PICKING;
        $order->time = Carbon::parse('2026-08-15 10:10', self::TZ)->getTimestamp();

        $order->validateTime('time', []);

        $this->assertFalse($order->hasErrors('time'));
    }
}
