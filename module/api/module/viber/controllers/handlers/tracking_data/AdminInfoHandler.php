<?php


namespace app\module\api\module\viber\controllers\handlers\tracking_data;


use app\module\admin\module\order\models\Order;
use app\module\admin\module\order\models\OrderProduct;
use app\module\api\module\viber\components\Button;
use app\module\api\module\viber\components\RichMedia;
use app\module\api\module\viber\controllers\handlers\tracking_data\traits\OrderInfoTrait;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\helpers\Str;
use app\module\api\module\viber\controllers\helpers\T;
use app\module\api\module\viber\controllers\senders\ViberSender;
use app\module\api\module\viber\interfaces\HandlerInterface;
use Carbon\Carbon;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\Url;

class AdminInfoHandler extends TrackingDataHandler implements HandlerInterface
{
    use OrderInfoTrait;

    /**
     * @param $v_m
     * @return bool
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function run($v_m)
    {
        //Init
        $this->v_m = $v_m;
        $messages = Helper::config('main.messages.admin_info');

        $message = json_decode($this->v_m->message, true);
        if ($this->v_m->type === 'text' && in_array($message['text'], array_keys($messages))) {
            if ($message['text'] === 'order_no') {
                $this->sendAskNo();
            } else {
                $this->SendOrderInfo($message);
            }

            $message['text'] = $messages[$message['text']];
            $this->v_m->update([
                'message' => json_encode($message)
            ]);
        }
        return true;
    }

    private function sendAskNo()
    {
        $this->send(['text' => T::t('viber', 'Enter order number, please.')], ['tracking_data' => 'order_no']);
    }
}