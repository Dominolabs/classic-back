<?php


namespace app\module\api\module\viber\controllers\handlers\events;


use Throwable;
use app\module\api\module\viber\controllers\handlers\ViberUserHandler;
use app\module\api\module\viber\interfaces\HandlerInterface;

class SubscribedHandler extends EventHandler implements HandlerInterface
{
    /**
     * @param $data
     * @return array|null
     * @throws Throwable
     */
    public function run($data)
    {
        $this->v_u = (new ViberUserHandler($this->chat))->run($data);
        return $this->sendSuccessResponse();
    }
}