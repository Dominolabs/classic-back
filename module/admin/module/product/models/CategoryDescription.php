<?php
/**
 * CategoryDescription model class file.
 */

namespace app\module\admin\module\product\models;

use Yii;

/**
 * This is the model class for table "tbl_category_description".
 *
 * @property int $category_id
 * @property int $language_id
 * @property string $name
 * @property string $description
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keyword
 */
class CategoryDescription extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_category_description';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'language_id'], 'required'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['category_id', 'language_id'], 'integer'],
            [['description'], 'string'],
            [['name', 'meta_title', 'meta_description', 'meta_keyword'], 'string', 'max' => 255],
            [['category_id', 'language_id'], 'unique', 'targetAttribute' => ['category_id', 'language_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => 'ID категории',
            'language_id' => 'ID языка',
            'name' => 'Название',
            'description' => 'Описание',
            'meta_title' => 'Мета-тег Title',
            'meta_description' => 'Мета-тег Description',
            'meta_keyword' => 'Мета-тег Keywords',
        ];
    }

    /**
     * Removes category descriptions by category id.
     *
     * @param string $categoryId category id
     */
    public static function removeByCategoryId($categoryId)
    {
        self::deleteAll(['category_id' => $categoryId]);
    }
}
