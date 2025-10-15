<?php

namespace app\controllers;

use app\components\LiqPay;
use app\components\OrderProductToLiqPayConverter;
use app\models\DbLog;
use app\module\admin\module\order\models\Order;
use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\traits\PrintTrait;
use yii\web\ErrorAction;

class OrderController extends Controller
{
    use PrintTrait;

    /**
     * @return array
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * @param Action $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if ($action->id === 'processing') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * @param int $order_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPayment($order_id): string
    {
        /** @var Order $order */
        $order = Order::find()->where(['order_id' => $order_id])->one();
        $error = null;
        $liqPayHtml = '';

        if (!empty($order) && (int)$order->payment_type === Order::PAYMENT_TYPE_ONLINE) {
            try {
                $liqpay = new LiqPay(Yii::$app->params['liqPayPublicKey'], Yii::$app->params['liqPayPrivateKey']);
                $config = [
                    'action' => 'pay',
                    'amount' => $order->total,
                    'currency' => LiqPay::CURRENCY_UAH,
                    'description' => Yii::t('order', 'Оплата замовлення Classic'),
                    'order_id' => $order->order_id,
                    'version' => '3',
                    'language' => Yii::$app->language,
                    'server_url' => Url::to('api/order/processing', 'https')
                ];
                $items = OrderProductToLiqPayConverter::getItems($order);
                if ($items) {
                    $config['rro_info']['items'] = $items;
                    if ($order->email) {
                        $config['rro_info']['delivery_emails'][] = $order->email;
                    }
                    if (!empty(Yii::$app->params['liqPayEmailsForCheck'])) {
                        $emails = preg_split('/, */', Yii::$app->params['liqPayEmailsForCheck']);
                        if ($emails) {
                            if (!is_array($emails)) {
                                $emails = [$emails];
                            }
                            $emails = array_filter($emails, function ($email) {
                                return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
                            });
                            $config['rro_info']['delivery_emails'] = array_merge($config['rro_info']['delivery_emails'] ?? [], $emails);
                        }
                    }
                }
                if (empty(Yii::$app->params['liqPaySandbox']) || (int)Yii::$app->params['liqPaySandbox'] === 0) {
                    $config['sandbox'] = '1';
                }
                try {
                    DbLog::add([
                        'msg' => json_encode($config),
                        'category' => 'OrderController::actionPayment'
                    ]);
                } catch (\Throwable $e) {
                    //Silent
                }
                $liqPayHtml = $liqpay->cnb_form($config);
            } catch (\Exception $exception) {
                $error = Yii::t('order', 'Не вдалося оплатити замовлення.');
            }
            return $this->renderPartial('payment', [
                'liqPayHtml' => $liqPayHtml,
                'error' => $error
            ]);
        }
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionProcessing()
    {
        $this->enableCsrfValidation = false;
        $data = Yii::$app->request->post('data');
        $signature = Yii::$app->request->post('signature');
        if (!empty($data) && !empty($signature)) {
            $sign = base64_encode(sha1(Yii::$app->params['liqPayPrivateKey'] . $data . Yii::$app->params['liqPayPrivateKey'], 1));
            // Success request
            if ($sign === $signature) {
                // Success
                $orderInfo = base64_decode($data);
                Yii::info('OrderInfo: ' . $orderInfo, 'liqpay');
                $orderInfo = json_decode($orderInfo, true);
                /** @var Order $order */
                $order = Order::find()->where(['order_id' => $orderInfo['order_id']])->one();
                if ($order) {
                    if ($orderInfo['status'] === 'success' || $orderInfo['status'] === 'sandbox') {
                        $order->payment_status = Order::PAYMENT_STATUS_PAID;
                        $order->save(false);
                        Yii::info('Success payment order # ' . $order->order_id, 'liqpay');
                        echo 'OK';
                    }
                    if (isset($orderInfo['err_code'], $orderInfo['err_description'])) {
                        Yii::info('Error from LiqPay ' . $orderInfo['err_code'] . ' ' . $orderInfo['err_description'], 'liqpay');
                    }

                    return;
                }
                throw new Exception('Order not found.');
            }
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        Yii::info('Error: signatures do not match.', 'liqpay');
    }
}
