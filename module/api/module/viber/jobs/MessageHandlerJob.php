<?php


namespace app\module\api\module\viber\jobs;


use app\module\api\module\viber\controllers\handlers\events\MessageHandler;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\module\api\module\viber\controllers\helpers\T;
use app\module\api\module\viber\models\ViberChat;
use \Yii;
use yii\base\BaseObject;
use yii\i18n\I18N;
use yii\queue\JobInterface;
use \yii\BaseYii;
use yii\queue\Queue;

class MessageHandlerJob extends BaseObject implements JobInterface
{
    public $data;
    public $chat_id;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        try {
            $data = unserialize(base64_decode($this->data));
            $chat = ViberChat::find()->where(['viber_chat_id' => $this->chat_id])->one();

            if ($chat) (new MessageHandler($chat))->work($data);
        } catch (\Throwable $e) {
            if (isset($data)) Helper::log($data, 'viber');
            Helper::log([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'viber');
        }
    }
}