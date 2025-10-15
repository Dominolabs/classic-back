<?php
/**
 * ProductDescription model class file.
 */

namespace app\module\admin\module\product\models;

use Yii;

/**
 * This is the model class for table "tbl_product_description".
 *
 * @property int $product_id
 * @property int $language_id
 * @property string $name
 * @property string $weight
 * @property string $description
 * @property string $promo
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keyword
 */
class ProductDescription extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_product_description';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'language_id'], 'required'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['product_id', 'language_id'], 'integer'],
            [['description', 'promo', 'weight'], 'string'],
            [['name', 'meta_title', 'meta_description', 'meta_keyword', 'weight'], 'string', 'max' => 255],
            [['product_id', 'language_id'], 'unique', 'targetAttribute' => ['product_id', 'language_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'ID товара',
            'language_id' => 'ID языка',
            'name' => 'Название',
            'weight' => 'Размер порции',
            'description' => 'Описание',
            'promo' => 'Акция',
            'meta_title' => 'Мета-тег Title',
            'meta_description' => 'Мета-тег Description',
            'meta_keyword' => 'Мета-тег Keywords',
        ];
    }

    /**
     * Removes product descriptions by product id.
     *
     * @param string $productId product id
     */
    public static function removeByProductId($productId)
    {
        self::deleteAll(['product_id' => $productId]);
    }
}
