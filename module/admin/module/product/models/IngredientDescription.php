<?php

namespace app\module\admin\module\product\models;

use yii\db\ActiveRecord;

/**
 * @property int $ingredient_id
 * @property int $language_id
 * @property string $name
 * @property string $portion_size
 */
class IngredientDescription extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_ingredient_description';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ingredient_id', 'language_id'], 'required'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['ingredient_id', 'language_id'], 'integer'],
            [['name', 'portion_size'], 'string', 'max' => 255],
            [['ingredient_id', 'language_id'], 'unique', 'targetAttribute' => ['ingredient_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ingredient_id' => 'ID ингредиента',
            'language_id' => 'ID языка',
            'name' => 'Название',
            'portion_size' => 'Размер порции',
        ];
    }

    /**
     * @param string $ingredientId
     */
    public static function removeByIngredientId($ingredientId): void
    {
        self::deleteAll(['ingredient_id' => $ingredientId]);
    }
}
