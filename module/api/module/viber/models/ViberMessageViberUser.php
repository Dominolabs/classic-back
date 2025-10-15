<?php

namespace app\module\api\module\viber\models;


/**
 * @property int $viber_message_viber_user_id
 * @property int $viber_message_id
 * @property int $viber_user_id
 * @property int $delivered_at
 * @property int $seen_at
 *
 */
class ViberMessageViberUser extends ActiveModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'viber_message_id',
                'viber_user_id',
                'delivered_at',
                'seen_at',
            ], 'default', 'value' => null],
            [[
                'viber_message_id',
                'viber_user_id',
                'delivered_at',
                'seen_at',
            ], 'integer'],
        ];
    }
}
