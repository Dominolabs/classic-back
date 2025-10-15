<?php

namespace app\module\admin\module\order\models;

/**
 * @property int $city_id
 * @property int $language_id
 * @property string $name
 *
 * @property City $city
 */
class CityDescription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_city_description';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id', 'language_id'], 'required'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['city_id', 'language_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['city_id', 'language_id'], 'unique', 'targetAttribute' => ['city_id', 'language_id']],
            [
                ['city_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => City::class,
                'targetAttribute' => ['city_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'city_id' => 'ID города',
            'language_id' => 'ID языка',
            'name' => 'Название',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * @param int $cityId
     */
    public static function removeByPageId($cityId)
    {
        self::deleteAll(['city_id' => $cityId]);
    }
}
