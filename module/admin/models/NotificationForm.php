<?php

namespace app\module\admin\models;

use Exception;
use yii\base\Model;

class NotificationForm extends Model
{
    /** @var string */
    public $header;
    /** @var string */
    public $message;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['message'], 'required'],
            [['header'], 'string', 'max' => 255],
            [['message'], 'string', 'max' => 10000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'header' => 'Заголовок',
            'message' => 'Сообщение',
        ];
    }

    /**
     * @return bool
     */
    public function send()
    {
        try {
            if ($this->validate()) {
                $userIds = [];
                $playerIds = [];
                $users = User::findAll(['role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE]);

                foreach ($users as $user) {
                    if (!empty($user['device_id']) && $user['device_id'] !== 'undefined' && $user['device_id'] !== 'null') {
                        $playerIds[] = $user['device_id'];
                        $userIds[] = $user['user_id'];
                    }
                }

                $playerIdsCount = count($playerIds);

                for ($i = 0; $i < $playerIdsCount; $i += 2000) {
                    $recipients = array_slice($playerIds, $i, 2000);

                    $tokens = [];
                    foreach ($recipients as $recipient) {
                        if (strpos($recipient, 'ExponentPushToken[') === 0) {
                            $tokens[] = $recipient;
                        }
                    }

                    User::sendExpoNotification($this->header, $this->message, $tokens);
                }

                $message = [
                    'text' => $this->message,
                ];

                User::addToNotificationsHistory($this->header, $message, $userIds);

                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
