<?php

namespace app\module\api\module\viber\models;

use app\module\admin\models\User;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * @property int $viber_user_id
 * @property int $user_id
 * @property string $viber_id
 * @property string $name
 * @property string $avatar
 * @property string $country
 * @property string $language
 * @property string $api_version
 * @property string $phone
 *
 * @property array $viberChats
 * @property array $viberMessages
 * @property User $user
 */
class ViberUser extends ActiveModel
{


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'api_version'], 'integer'],
            [[
                'viber_id',
                'name',
                'avatar',
                'country',
                'language',
                'phone',
                ], 'string'],
        ];
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getViberMessages()
    {
        return$this->hasMany(ViberMessage::class, ['viber_message_id' => 'viber_message_id'])
            ->viaTable('{{%viber_message_viber_user}}', ['viber_user_id' => 'viber_user_id']);
    }


    public function getViberChatViberUser()
    {
        return $this->hasOne(ViberChatViberUser::class, ['viber_user_id' => 'viber_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getViberChats()
    {
        return $this->hasMany(ViberChat::class, ['viber_chat_id' => 'viber_chat_id'])
            ->via('viberChatViberUser')->with('viberChatViberUser');
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['user_id' => 'user_id']);
    }
}
