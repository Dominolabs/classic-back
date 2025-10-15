<?php

namespace app\module\admin\models;

use Yii;

/**
 * This is the model class for table "{{%page_description}}".
 *
 * @property int $page_id
 * @property int $language_id
 * @property string $title
 * @property string $description1
 * @property string $description2
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keyword
 */



class PageDescription extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page_description}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'language_id'], 'required'],
            [['title'], 'required', 'on' => 'language-is-system'],
            [['page_id', 'language_id'], 'integer'],
            [['description1', 'description2'], 'string'],
            [['title'], 'string', 'max' => 128],
            [['meta_title', 'meta_description', 'meta_keyword'], 'string', 'max' => 255],
            [['page_id', 'language_id'], 'unique', 'targetAttribute' => ['page_id', 'language_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' => 'ID страницы',
            'language_id' => 'ID языка',
            'title' => 'Название',
            'description1' => 'Описание (первая колонка)',
            'description2' => 'Описание (вторая колонка)',
            'meta_title' => 'Мета-тег Title',
            'meta_description' => 'Мета-тег Description',
            'meta_keyword' => 'Мета-тег Keywords',
        ];
    }

    /**
     * Removes page descriptions by page id.
     *
     * @param string $pageId page id
     */
    public static function removeByPageId($pageId)
    {
        self::deleteAll(['page_id' => $pageId]);
    }
}
