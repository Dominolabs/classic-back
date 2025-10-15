<?php

namespace app\module\api\module\viber\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * @property int $viber_chat_id
 * @property string $chat_number
 * @property string $chat_hostname
 * @property string $name
 * @property string $uri
 * @property string $icon
 * @property string $background
 * @property string $category
 * @property string $subcategory
 * @property string $location
 * @property string $country
 * @property string $webhook
 * @property string $token
 *
 * @property array $viberUsers
 * @property array $viberMessages
 */
class ViberChat extends ActiveModel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'chat_number',
                'name',
                'uri',
                'icon',
                'background',
                'category',
                'subcategory',
                'location',
                'country',
                'webhook',
                'token',
            ], 'default', 'value' => ''],
            [[
                'chat_number',
                'name',
                'uri',
                'icon',
                'background',
                'category',
                'subcategory',
                'location',
                'country',
                'webhook',
                'token',
            ], 'string'],
        ];
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getViberUsers()
    {
        return $this->hasMany(ViberUser::class, ['viber_user_id' => 'viber_user_id'])
            ->viaTable('{{%viber_chat_viber_user}}', ['viber_chat_id' => 'viber_chat_id']);
    }

    public function getViberMessages()
    {
        return $this->hasMany(ViberMessage::class, ['viber_chat_id' => 'viber_chat_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getViberChatViberUser()
    {
        return $this->hasMany(ViberChatViberUser::class, ['viber_chat_id' => 'viber_chat_id'])
            ->select(ViberChatViberUser::tableName() . '.subscribed_at, ' . ViberChatViberUser::tableName() . '.unsubscribed_at');
    }
    public static function getChat($chat = null)
    {
        if (is_null($chat)) {
            $chat = Yii::$app->request->get('chat');
            if (empty($chat)) $chat = Yii::$app->request->post('chat');
        }

        if (!empty($chat)) {
            $chat = ViberChat::where(['viber_chat_id' => $chat])->one();
        }
        return $chat;
    }
}
