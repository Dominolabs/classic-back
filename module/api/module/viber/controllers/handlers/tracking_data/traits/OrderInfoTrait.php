<?php


namespace app\module\api\module\viber\controllers\handlers\tracking_data\traits;


use app\module\admin\module\order\models\Order;
use app\module\admin\module\order\models\OrderProduct;
use app\module\api\module\viber\components\Button;
use app\module\api\module\viber\components\RichMedia;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\helpers\Str;
use app\module\api\module\viber\controllers\helpers\T;
use app\module\api\module\viber\controllers\senders\ViberSender;
use Carbon\Carbon;
use Yii;
use yii\db\ActiveRecord;

trait OrderInfoTrait
{
    public $next_t_d = 'message';

    /**
     * @param $message
     * @throws \ReflectionException
     */
    public function SendOrderInfo($message)
    {
        $info = $this->getModels($message['text']);
        $count = 1;
        if (!empty($info)) {
            /** @var Order $order */
            foreach ($info as $order) {
                $this->send(['text' => $count++ . '. ' . $this->getOrderInfoMessage($order)], ['tracking_data' => $this->next_t_d]);
                sleep(2);
                if ($this->v_u->api_version >= 6.7) {
                    $chunks = $order->getOrderProducts()->batch(6);

                    foreach ($chunks as $products) {
                        if (!empty($arr = $this->getProductsList($products, $order->currency_code)))
                            $this->send(['rich_media' => $arr], ['min_api_version' => 2, 'tracking_data' => 'message']);
                    }
                }
            }
        } else ViberSender::sendNotFoundMessage($this->v_u, $this->v_m->viber_chat_id);
    }

    /**
     * @param Order $order
     * @return string
     */
    public static function getOrderInfoMessage(Order $order)
    {
        $result = '';

        $arr = ['order_id', 'name', 'email', 'phone', 'city_name', 'street', 'entrance', ' house_number', 'apartment_number', 'comment',];

        foreach ($arr as $value) {
            if (!empty($order->$value))
                $result .= T::t('order', str_replace('_', ' ', ucfirst($value))) . ': ' . $order->$value . "\n";
        }

        if (!empty($order->restaurant_id)) {
            $result .= T::t('order', "Restaurant") . ': ' . $order->getRestaurantName() . "\n";
        }

        if (!empty($order->time))
            $result .= T::t('order', "Time") . ': ' . Yii::$app->formatter->asDate($order->time, 'php:d.m.Y H:i:s') . "\n";

        if (!empty($order->total))
            $result .= T::t('order', "Total") . ': ' . round($order->total, 2) . ' ' . $order->currency_code . "\n";

        if (!empty($order->payment_type))
            $result .= T::t('order', "Payment type") . ': ' . Order::getPaymentTypeName($order->payment_type) . "\n";

        if (!is_null($order->payment_status))
            $result .= T::t('order', "Payment status") . ': ' . Order::getPaymentStatusName($order->payment_status) . "\n";

        $result .= "\n" . Str::finish(Helper::SITE_URL, '/') . 'admin/order/order/update?id=' . $order->order_id;

        return $result;
    }

    /**
     * @param $products
     * @param $currency_code
     * @return array
     * @throws \ReflectionException
     */
    public static function getProductsList($products, $currency_code)
    {
        $rich_media = new RichMedia(['ButtonsGroupRows' => 6,]);
        /** @var OrderProduct $product */
        foreach ($products as $product) {
            if (!empty($product->properties) && !empty($arr = json_decode($product->properties, true))) {
                $s = '(' . ($arr[0]['property']['uk'] ?? ($arr['property']['uk'] ?? '')) . ' соус) ';
            } else $s = '';
            $prod_text = '<b>' . $product->name . " $s" .  '</b><br>'
//                . $product->quantity . 'x' . round($product->price, 2) . ' ' . $order->currency_code . "<br>"
                . T::t('order', 'Total') . ': ' . round($product->total, 2) . ' ' . $currency_code;
            $rich_media->attach(new Button([
                'Columns' => 1,
                'Rows' => 4,
                'ActionType' => 'none',
                'BgColor' => $rich_media->BgColor,
            ]))->attach(new Button([
                'Columns' => 4,
                'Rows' => 4,
                'ActionType' => 'none',
                'Image' => $product->viberImgUrl,
                'ImageScaleType' => 'fill'
            ]))->attach(new Button([
                'Columns' => 1,
                'Rows' => 4,
                'ActionType' => 'none',
                'BgColor' => $rich_media->BgColor,
            ]))->attach(new Button([
                'Rows' => 2,
                'ActionType' => 'none',
                'Text' => $prod_text
            ]));

        }

        return $rich_media->makeArray();
    }

    /**
     * @param $text
     * @return array|ActiveRecord[]
     */
    private function getModels($text)
    {
        $actions = [
            'ten_orders' => 10,
            'last_order' => 1,
        ];
        if (in_array($text, array_keys($actions)))
            return Order::find()->with('orderProducts')->orderBy(['created_at' => SORT_DESC])->limit($actions[$text] ?? 1)->all();

        return Order::find()->with('orderProducts')->where(['order_id' => $text])->limit(1)->all();
    }
}