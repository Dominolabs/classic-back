<?php

namespace app\module\admin\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "tbl_product_description".
 *
 * @property int $product_id
 * @property int $language_id
 * @property string $name
 * @property string $description
 * @property string $promo
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keyword
 */
class ClassicDescription extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'tbl_classic_description';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['product_id', 'language_id'], 'required'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['product_id', 'language_id'], 'integer'],
            [['description', 'promo'], 'string'],
            [['name', 'meta_title', 'meta_description', 'meta_keyword'], 'string', 'max' => 255],
            [['product_id', 'language_id'], 'unique', 'targetAttribute' => ['product_id', 'language_id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'product_id' => 'ID товара',
            'language_id' => 'ID языка',
            'name' => 'Название',
            'description' => 'Описание',
            'promo' => 'Акция',
            'meta_title' => 'Мета-тег Title',
            'meta_description' => 'Мета-тег Description',
            'meta_keyword' => 'Мета-тег Keywords',
        ];
    }

    /**
     * @param string $productId
     * @return void
     */
    public static function removeByProductId($productId): void
    {
        self::deleteAll(['product_id' => $productId]);
    }
}
