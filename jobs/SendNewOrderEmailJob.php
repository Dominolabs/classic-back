<?php

namespace app\jobs;

use app\module\admin\module\order\models\Order;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SendNewOrderEmailJob extends BaseObject implements JobInterface
{
    public $order_id;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        try {
            $siteName = isset(Yii::$app->params['siteName']) ? Yii::$app->params['siteName'] : Yii::$app->name;
            $order = Order::findOne($this->order_id);

            if ($order) {
                $res = Yii::$app
                ->mailer
                ->compose(
                    ['html' => '@app/mail/frontend/adminNewOrder-html', 'text' => '@app/mail/frontend/adminNewOrder-text'],
                    [
                        'order' => $order,
                        'orderProducts' => $order->orderProducts,
                    ]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => $siteName . ' робот'])
                ->setTo(Yii::$app->params['adminEmail'])
                ->setSubject('Новый заказ № ' . $this->order_id)
                ->send();
            }
        } catch (Throwable $e) {
            Yii::info([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'emails');
        }
    }
}