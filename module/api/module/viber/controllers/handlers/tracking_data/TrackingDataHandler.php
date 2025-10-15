<?php


namespace app\module\api\module\viber\controllers\handlers\tracking_data;

use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\senders\ViberSender;
use app\module\api\module\viber\models\ViberMessage;
use app\module\api\module\viber\models\ViberUser;

/**
 * Class TrackingDataHandler
 * @package app\module\api\module\viber\controllers\handlers\tracking_data
 * @property ViberUser $v_u
 * @property ViberMessage $v_m
 */
class TrackingDataHandler
{
    public $v_u;
    public $v_m;

    public function __construct($v_u)
    {
        $this->v_u = $v_u;
    }

    /**
     * @param $data
     * @param array $additional
     */
    public function send($data, $additional = [])
    {
        $data = array_merge([
            'to' => $this->v_u->viber_id,
        ], $data);
        ViberSender::sendMessage(
            $data,
            $this->v_m->viber_chat_id,
            $additional);
    }
}