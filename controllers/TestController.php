<?php


namespace app\controllers;


use app\jobs\SendNewOrderEmailJob;
use app\module\admin\models\User;
use app\module\admin\module\order\models\Order;
use app\module\api\module\viber\controllers\handlers\tracking_data\traits\OrderInfoTrait;
use app\module\api\module\viber\controllers\senders\ViberSender;
use Yii;
use yii\base\Controller;
use yii\helpers\Url;
use yii\web\View;

class TestController extends Controller
{
    public function actionDo()
    {
        $recipients[0] = 'lightisthebest@gmail.com';
        $message = 'test';
        $header = 'test';
        $headers = "X-Mailer:USER_AGENT_MOZILLA_XM\r\n";
        $headers .= "User-Agent:USER_AGENT_MOZILLA_UA\r\n";
        $headers .= "From: " . Yii::$app->name . ' робот' . " <".Yii::$app->params['supportEmail']. ">\r\n";
        foreach ($recipients as $id => $email) {
            $message .= "\n" . Yii::t('common', "If you don't want receive more letter click on the link below.") . "\n";
            $message .= Url::to("unsubscribe?user=$id", 'https');
            mail($email, $header, $message, $headers);
        }
        return 'ok';
    }
}