<?php
/**
 * ProductToCategory model class file.
 */

namespace app\module\admin\module\product\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "tbl_product_to_category".
 *
 * @property int $product_id
 * @property int $category_id
 *
 * @property Category $category
 */
class ProductToCategory extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'tbl_product_to_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['product_id', 'category_id'], 'integer'],
            [['product_id', 'category_id'], 'unique', 'targetAttribute' => ['product_id', 'category_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'ID товара',
            'category_id' => 'Категория',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['category_id' => 'category_id']);
    }

    /**
     * Removes product to category relations by product id.
     *
     * @param string $productId product id
     */
    public static function removeByProductId($productId)
    {
        self::deleteAll(['product_id' => $productId]);
    }

    /**
     * Removes product to category relations by category id.
     *
     * @param string $categoryId category id
     */
    public static function removeByCategoryId($categoryId)
    {
        self::deleteAll(['category_id' => $categoryId]);
    }

    /**
     * Returns category id by product id.
     *
     * @param int $productId product id
     * @return false|null|string category id
     */
    public static function getCategoryIdByProductId($productId)
    {
        return (new Query())
            ->select('category_id')
            ->from(self::tableName())
            ->where(['product_id' => $productId])
            ->scalar();
    }
}
