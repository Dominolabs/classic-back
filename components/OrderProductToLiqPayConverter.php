<?php

namespace app\components;

use app\models\DbLog;
use app\module\admin\models\Classic;
use app\module\admin\module\order\models\City;
use app\module\admin\module\order\models\Order;
use app\module\admin\module\order\models\OrderProduct;
use app\module\admin\module\product\models\Ingredient;
use Exception;
use Throwable;
use Yii;

class OrderProductToLiqPayConverter
{
    private const TEST = false;
    private const DEFAULT_PB_ID = self::TEST ? 123456 : null;

    /**
     * @param OrderProduct $product
     * @return array
     * @throws Exception
     */
    protected static function convert(OrderProduct $product): array
    {
        if ($product->type == 'classic') {
            $pbId = Classic::find()->where(['product_id' => 1])->one()->pb_id;
        } else {
            $pbId = $product->product->pb_id ?? self::DEFAULT_PB_ID;
        }
        if (empty($pbId)) {
            throw new Exception('Empty PrivatBank ID for product ' . $product->product_id);
        }
        $items = [];
        $items[] = [
            "amount" => $product->quantity,
            "price" => round($product->price, 2),
            "cost" => round($product->price, 2) * (int)$product->quantity,
            "id" => (int)$pbId
        ];
        $ingredients = !is_array($product->ingredients) ? json_decode($product->ingredients, true) : [];
        if (!is_array($ingredients)) {
            $ingredients = is_null($ingredients) ? [] : [$ingredients];
        }

        if ($product->type == 'classic') {
            $current = $ingredients['additional_ingredients'] ?? null;
            foreach ($current as $ingredient) {
                $pbId = $ingredient['pb_id'] ?? self::DEFAULT_PB_ID;
                $id = $ingredient['ingredient_id'] ?? null;
                if (empty($pbId)) {
                    $pbId = Ingredient::findOne(['ingredient_id' => $id])->pb_id ?? null;
                }
                if (empty($pbId)) {
                    throw new Exception('Empty PrivatBank ID for ingredient ' . $id);
                }
                $items[] = [
                    "amount" => $ingredient['quantity'],
                    "price" => round($ingredient['price'], 2),
                    "cost" => round($ingredient['quantity'] * $ingredient['price'],  2),
                    "id" => (int)$pbId
                ];
            }
        } else {
            foreach (['main_ingredients', 'additional_ingredients'] as $group) {
                $current = $ingredients[$group] ?? null;
                if (!is_array($current)) {
                    continue;
                }
                foreach ($current as $ingredient) {
                    $pbId = $ingredient['pb_id'] ?? self::DEFAULT_PB_ID;
                    $id = $ingredient['ingredient_id'] ?? null;
                    if (empty($pbId)) {
                        $pbId = Ingredient::findOne(['ingredient_id' => $id])->pb_id ?? null;
                    }
                    if (empty($pbId)) {
                        throw new Exception('Empty PrivatBank ID for ingredient ' . $id);
                    }
                    $items[] = [
                        "amount" => $ingredient['quantity'],
                        "price" => round($ingredient['price'], 2),
                        "cost" => round($ingredient['quantity'] * $ingredient['price'],  2),
                        "id" => (int)$pbId
                    ];
                }
            }
        }
        return $items;
    }
    /**
     * @param $city
     * @return array
     * @throws Exception
     */
    protected static function convertCity($city): array
    {
        $pbId = $city->pb_id ?? self::DEFAULT_PB_ID;
        if (empty($pbId)) {
            throw new Exception('Empty PrivatBank ID for city ' . $city->id);
        }
        $items = [];
        $items[] = [
            "amount" => 1,
            "price" => round($city->delivery_price, 2),
            "cost" => round($city->delivery_price, 2),
            "id" => (int)$pbId
        ];
        return $items;
    }

    public static function getItems(Order $order): array
    {
        if (empty(Yii::$app->params['liqPaySendRRO'])) {
            return [];
        }
        $items = [];
        try {
            /** @var OrderProduct $product */

            foreach ($order->orderProducts ?? [] as $product) {
                $items = array_merge($items, OrderProductToLiqPayConverter::convert($product));
            }
            $totalCost = array_sum(array_column($items, 'cost'));
            if ($order->delivery_type == Order::DELIVERY_TYPE_ADDRESS && $order->city->free_minimum_order > $totalCost) {
                $items = array_merge($items, OrderProductToLiqPayConverter::convertCity($order->city));
            }
        } catch (Throwable $e) {
            DbLog::add([
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'category' => 'OrderProductToLiqPayConverter::getItems'
            ]);
            return [];
        }
        return $items;
    }
}