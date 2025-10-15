<?php

namespace app\module\admin\models;

/**
 * @property int $restaurant_id
 * @property int $language_id
 * @property string $title
 * @property string $description1
 * @property string $description2
 * @property string $address
 * @property string $phone
 * @property string $schedule
 * @property string $gmap
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keyword
 */
class RestaurantDescription extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%restaurant_description}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['restaurant_id', 'language_id'], 'required'],
            [['title'], 'required', 'on' => 'language-is-system'],
            [['restaurant_id', 'language_id'], 'integer'],
            [['description1', 'description2', 'schedule', 'gmap'], 'string'],
            [['title'], 'string', 'max' => 128],
            [['address', 'phone', 'meta_title', 'meta_description', 'meta_keyword'], 'string', 'max' => 255],
            [['restaurant_id', 'language_id'], 'unique', 'targetAttribute' => ['restaurant_id', 'language_id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'restaurant_id' => 'ID страницы',
            'language_id' => 'ID языка',
            'title' => 'Название',
            'description1' => 'Описание (первая колонка)',
            'description2' => 'Описание (вторая колонка)',
            'address' => 'Адрес',
            'phone' => 'Номер телефона',
            'schedule' => 'График работы',
            'gmap' => 'Ссылка на карту Google Maps',
            'meta_title' => 'Мета-тег Title',
            'meta_description' => 'Мета-тег Description',
            'meta_keyword' => 'Мета-тег Keywords',
        ];
    }

    /**
     * @param string $restaurantId
     * @return void
     */
    public static function removeByRestaurantId($restaurantId): void
    {
        self::deleteAll(['restaurant_id' => $restaurantId]);
    }
}
