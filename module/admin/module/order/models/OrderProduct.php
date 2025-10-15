<?php

namespace app\module\admin\module\order\models;

use app\components\ImageBehavior;
use app\module\admin\module\product\models\Product;
use app\module\api\module\viber\controllers\helpers\Helper;
use app\jobs\ImageCopiesJob;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * @property int $order_product_id
 * @property int $order_id
 * @property int $product_id
 * @property int $category_id
 * @property int $product_type
 * @property string $name
 * @property int $weight_dish
 * @property int $quantity
 * @property string $price
 * @property string $total
 * @property string $type
 * @property string $ingredients
 * @property string $properties
 *  * @property string $comment
 *
 * @property string $viberImgUrl
 * @property Product $product
 */
class OrderProduct extends ActiveRecord
{
    public const NO = 0;
    public const YES = 1;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'tbl_order_product';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['order_id', 'name', 'quantity'], 'required'],
            [['order_id', 'weight_dish', 'product_id', 'category_id', 'product_type'], 'integer'],
            [['quantity'], 'integer', 'min' => 1, 'max' => 9999],
            [['price', 'total'], 'number', 'min' => 0],
            [['name', 'type'], 'string', 'max' => 255],
            [['ingredients', 'properties', 'comment'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'order_product_id' => 'ID товара в заказе',
            'order_id' => '№ заказа',
            'product_id' => 'Товар',
            'category_id' => 'Категория',
            'product_type' => 'Вариант',
            'name' => 'Товар',
            'weight_dish' => 'Весовое блюдо',
            'quantity' => 'Количество',
            'price' => 'Цена за единицу	',
            'total' => 'Итого',
            'ingredients' => 'Ингредиенты',
            'properties' => 'Характеристики',
            'comment' => 'Комментарий'
        ];
    }

    /**
     * @param string $lang
     * @return string
     */
    public function getProductTypeString(string $lang = 'ru')
    {
        $pizzaCategoryId = (int)Yii::$app->params['pizzaCategoryId'] ?: 0;
        $noodlesCategoryId = (int)Yii::$app->params['noodlesCategoryId'] ?: 0;

        if ((int)$this->category_id === $pizzaCategoryId) {
            $price1 = [
                'uk' => 'Середня',
                'ru' => 'Средняя'
            ];
            $price2 = [
                'uk' => 'Велика',
                'ru' => 'Большая'
            ];
        } elseif ((int)$this->category_id === $noodlesCategoryId) {
            $price1 = [
                'uk' => 'Гостра',
                'ru' => 'Острая'
            ];
            $price2 = [
                'uk' => 'Не гостра',
                'ru' => 'Не острая'
            ];
        } else {
            $price1 = [
                'uk' => '',
                'ru' => ''
            ];
            $price2 = [
                'uk' => '',
                'ru' => ''
            ];
        }

        switch ($this->product_type) {
            case 1:
                return $price1[$lang] ?? '';
            case 2:
                return $price2[$lang] ?? '';
            default:
                return '';
        }
    }

    /**
     * @param string $orderId
     */
    public static function removeByOrderId($orderId): void
    {
        self::deleteAll(['order_id' => $orderId]);
    }

    /**
     * @param string $tableName
     * @return bool|int
     */
    public static function getAutoIncrement($tableName)
    {
        $res = (new Query())->select('AUTO_INCREMENT')
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where("TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $tableName . "'")
            ->one();

        if ($res) {
            return $res['AUTO_INCREMENT'];
        }

        return false;
    }


    /**
     * @return bool
     */
    public function isWeighDish(): bool
    {
        return $this->weight_dish === static::YES;
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['product_id' => 'product_id']);
    }

    /**
     * @return string
     */
    public function getViberImgUrl()
    {
        try {
            $url = $this->product->image ?? '';
            if (empty($url)) return Helper::asset('image/logo/avatar.jpg');

            if (file_exists(Yii::getAlias('@app/web/image/product/' . $url . '_xs.' . ImageBehavior::getExtension($url)))){
                return Helper::asset('image/product/' . $url . '_xs.' . ImageBehavior::getExtension($url));
            } else {
                $filename = Yii::getAlias('@app/web/image/product/' . $url);
                Yii::$app->queue->push(new ImageCopiesJob([
                    'file' => $filename,
                    'message' => 'Viber'
                ]));
            }
            return Helper::asset('image/product/' . $url);
        } catch (\Throwable $e) {
            Yii::info([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 'images');
            return Helper::asset('image/logo/avatar.jpg');
        }
    }
}
