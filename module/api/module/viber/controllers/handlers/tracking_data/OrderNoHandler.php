<?php


namespace app\module\api\module\viber\controllers\handlers\tracking_data;


use app\module\api\module\viber\controllers\handlers\tracking_data\traits\OrderInfoTrait;
use app\module\api\module\viber\interfaces\HandlerInterface;

class OrderNoHandler extends TrackingDataHandler implements HandlerInterface
{
    use OrderInfoTrait;

    /**
     * @param $v_m
     * @return bool
     * @throws \ReflectionException
     */
    public function run($v_m)
    {
        //Init
        $this->v_m = $v_m;

        $message = json_decode($this->v_m->message, true);

        if ($this->v_m->type === 'text') {
            $this->SendOrderInfo($message);
        }
        return true;
    }
}