<?php

namespace app\module\admin\models;

/**
 * @property int $restaurant_category_id
 * @property int $language_id
 * @property string $name
 */
class RestaurantCategoryDescription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_restaurant_category_description';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['restaurant_category_id', 'language_id'], 'required'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['restaurant_category_id', 'language_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['restaurant_category_id', 'language_id'], 'unique', 'targetAttribute' => ['restaurant_category_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'restaurant_category_id' => 'ID категории альбома',
            'language_id' => 'ID языка',
            'name' => 'Название',
        ];
    }

    /**
     * Removes album categories by album category id.
     *
     * @param string $restaurantCategoryId album category id
     */
    public static function removeByRestaurantCategoryId($restaurantCategoryId)
    {
        self::deleteAll(['restaurant_category_id' => $restaurantCategoryId]);
    }
}
