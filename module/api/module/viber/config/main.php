<?php

use app\module\api\module\viber\controllers\handlers\traits\NewConversationTrait;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\helpers\T;

$avatar = "/image/logo/avatar.jpg";


 return [
     'sender' => [
         'name' => 'Classic-робот',
         'avatar' => Helper::asset($avatar)
     ],
     'messages' => [
         'new_conversation' => [
             "0" => T::t('viber', 'No'),
             "-1" => T::t('viber', 'Try again'),
             "-2" => T::t('viber', 'Not in the list'),
             NewConversationTrait::$action_body => T::t('viber', 'Share phone')
         ],
         'admin_info' => [
             'ten_orders' => T::t('viber', 'Last ten orders'),
             'last_order' => T::t('viber', 'Last order'),
             'order_no' => T::t('viber', 'Order №...')
         ]
     ],
 ];