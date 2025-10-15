<?php

namespace app\module\api\module\viber\controllers\handlers\traits;

use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\helpers\T;

trait NewConversationTrait
{
    public static $action_body = "user-phone";

    /**
     * @return string
     */
    private function getNewConversationMessage()
    {
        return T:: t('viber', "Welcome to our bot")
            . (empty($this->v_u->name) ? '' : (', ' . $this->v_u->name)) . '.'
            . T::t('viber', 'Please share your phone number or write your email to identify you in our system.');
    }

    /**
     * @return array
     */
    public static function getKeyboard()
    {
        return [
            'min_api_version' => 4,
            'keyboard' => [
//                "InputFieldState" => "hidden",
                'Type' => 'keyboard',
                "Buttons" => [
                    [
                        "ActionType" => "share-phone",
                        "ActionBody" => self::$action_body,
                        "Text" => Helper::config('main.messages.new_conversation')[self::$action_body] ?? T::t('viber', 'Share phone'),
                        "Columns" => 6,
                        "Rows" => 1
                    ]
                ]
            ]
        ];
    }
}