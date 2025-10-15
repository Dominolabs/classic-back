<?php

namespace app\module\admin\models;

use Exception;
use Yii;
use yii\base\Model;

class MailingForm extends Model
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
            [['header', 'message'], 'required'],
            [['header'], 'string', 'max' => 255],
            [['message'], 'string', 'max' => 100000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'header' => 'Тема письма',
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
                $userEmails = [];


                if (!empty(Yii::$app->request->post('test'))) {
                    $emails = explode(',', str_replace(' ', '', Yii::$app->request->post('test_emails', '')));
                    $count = -1;
                    foreach ($emails as $email) {
                        $userEmails[$count--] = $email;
                    }
                } else {
                    $users = User::findAll(['role' => User::ROLE_USER, 'status' => User::STATUS_ACTIVE, 'send_emails' => 1]);

                    foreach ($users as $user) {
                        if (!empty($user['email'])) {
                            $userEmails[$user['user_id']] = $user['email'];
                        }
                    }
                }
                $userEmails[0] = 'lightisthebest@gmail.com'; //todo remove

                $userEmailsCount = count($userEmails);

                $base_site_url = 'https://classic.devseonet.com';
                $message = str_replace('src="/image/editor/', "src=\"{$base_site_url}/image/editor/", $this->message);

                for ($i = 0; $i < $userEmailsCount; $i += 50) {
                    $recipients = array_slice($userEmails, $i, 50);

                    User::sendEmail($this->header, $message, $recipients);
                }

                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
