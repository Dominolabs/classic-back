<?php


namespace app\module\api\module\viber\controllers\handlers\events;


use app\module\api\module\viber\controllers\traits\ResponseTrait;
use app\module\api\module\viber\models\ViberChat;
use app\module\api\module\viber\models\ViberUser;

/**
 * Class EventHandler
 * @package app\module\api\module\viber\controllers\handlers\events
 * @property ViberUser $v_u
 * @property ViberChat $chat
 */
class EventHandler
{
    use ResponseTrait;

    public $chat;
    public $v_u;

    public function __construct($chat)
    {
        $this->chat = $chat;
    }

    /**
     * @return array|null
     */
    protected function sendSuccessResponse()
    {
        return self::jsonResponse([
            'status' => 200,
            'message' => 'Success!'
        ], 200);
    }
}