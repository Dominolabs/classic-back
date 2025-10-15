<?php


namespace app\module\api\module\viber\models;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * Class ViberMessage
 * @package app\models\Viber
 * @property int $viber_message_id
 * @property int $viber_chat_id
 * @property string $message_token
 * @property string $sender
 * @property string $message_type
 * @property string $message
 * @property string $tracking_data
 * @property int $sent_at
 * @property string $type
 *
 * @property ViberChat $viberChat
 * @property ViberChat $chat
 * @property array $viberUsers
 */
class ViberMessage extends ActiveModel
{
    public $defaultDir = 'files/viber';

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['message_token'], 'default'],
            [[
                'viber_chat_id',
                'sent_at',
                'message_token'
            ], 'integer'],
            [[
                'sender',
                'message_type',
                'message',
                'tracking_data',
                'type',
            ], 'string'],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getViberChat()
    {
        return $this->hasOne(ViberChat::class, ['viber_chat_id' => 'viber_chat_id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getViberUsers()
    {
        return $this->hasMany(ViberUser::class, ['viber_user_id' => 'viber_user_id'])
            ->viaTable('{{%viber_message_viber_user}}', ['viber_message_id' => 'viber_message_id']);
    }
}
