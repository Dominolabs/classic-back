<?php


namespace app\module\api\jobs;


use app\module\admin\module\order\models\Order;
use app\module\api\controllers\traits\ResponseTrait;
use app\traits\PrintTrait;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class PrintOrderJob extends BaseObject implements JobInterface
{
    use PrintTrait;
    public $order_id;
    public $text;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        try {
            $order = Order::findOne($this->order_id);
            if ($order) $this->printOrder($order, $this->text);
        } catch (\Throwable $e) {
            Yii::info([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'printer');
        }

        // TODO: Implement execute() method.
    }
}