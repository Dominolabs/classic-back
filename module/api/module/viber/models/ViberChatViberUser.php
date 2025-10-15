<?php

namespace app\module\api\module\viber\models;


/**
 * @property int $viber_chat_viber_user_id
 * @property int $viber_chat_id
 * @property int $viber_user_id
 * @property int $subscribed_at
 * @property int $unsubscribed_at
 *
 */
class ViberChatViberUser extends ActiveModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'viber_chat_id',
                'viber_user_id',
                'subscribed_at',
                'unsubscribed_at',
            ], 'default', 'value' => null],
            [[
                'viber_chat_id',
                'viber_user_id',
                'subscribed_at',
                'unsubscribed_at',
            ], 'integer'],
        ];
    }
}
