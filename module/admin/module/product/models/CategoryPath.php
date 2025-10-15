<?php
/**
 * CategoryPath model class file.
 */

namespace app\module\admin\module\product\models;

use Yii;

/**
 * This is the model class for table "tbl_category_path".
 *
 * @property int $category_id
 * @property int $path_id
 * @property string $level
 */
class CategoryPath extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_category_path';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'path_id', 'level'], 'required'],
            [['category_id', 'path_id'], 'integer'],
            [['level'], 'string', 'max' => 255],
            [['category_id', 'path_id'], 'unique', 'targetAttribute' => ['category_id', 'path_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => 'Category ID',
            'path_id' => 'Path ID',
            'level' => 'Level',
        ];
    }

    /**
     * Returns all data by category id.
     *
     * @param int $categoryId category id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAllByCategoryId($categoryId)
    {
        return self::find()->where(['category_id' => $categoryId])->orderBy('level ASC')->all();
    }

    /**
     * Removes category descriptions by category id.
     *
     * @param string $categoryId category id
     */
    public static function removeByCategoryId($categoryId)
    {
        self::deleteAll(['category_id' => $categoryId]);

        $paths = CategoryPath::find()->where(['path_id' => $categoryId])->all();

        /** @var CategoryPath $path */
        foreach ($paths as $path) {
            self::removeByCategoryId($path->category_id);
        }
    }
}
