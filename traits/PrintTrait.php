<?php

namespace app\traits;

use app\module\admin\module\order\models\Order;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;
use Throwable;
use Yii;
use yii\db\Exception;

trait PrintTrait
{
    /**
     * @param Order $order
     */
    public function printOrder(Order $order, $text)
    {
        try {
            $text = base64_decode($text);
            $ip = Yii::$app->params['printerIp'] ?? null;
            $port = Yii::$app->params['printerPort'] ?? null;
            if ($ip && $port) {
                $connector = new NetworkPrintConnector($ip, $port);
                $profile = CapabilityProfile::load('default');

                $printer = new Printer($connector, $profile);

                try {
                    $printer->initialize();
                    $printer->selectCharacterTable(73);

                    $text = iconv('UTF-8', 'windows-1251//TRANSLIT//IGNORE', $text);
                    Yii::info('Before text raw ' . $order->order_id, 'printer');
                    $printer->textRaw($text);
                    $printer->cut();

                    Yii::info('Successfully printed order # ' . $order->order_id, 'printer');
                } catch (Throwable $e) {
                    Yii::error([
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ], 'printer');
                    throw new Exception($e->getMessage(), [], 0, $e);
                } finally {
                    $connector->finalize();
                    $printer->close();
                }
            } else {
                Yii::error('Empty printer IP or port', 'printer');
            }
        } catch (Throwable $exception) {
            Yii::error([
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ], 'printer');
        }
    }
}
