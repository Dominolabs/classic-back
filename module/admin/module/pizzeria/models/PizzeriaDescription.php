<?php

namespace app\module\admin\module\pizzeria\models;

/**
 * This is the model class for table "tbl_pizzeria_description".
 *
 * @property int $pizzeria_id
 * @property int $language_id
 * @property string $name
 * @property string $address
 * @property string $schedule
 */
class PizzeriaDescription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_pizzeria_description';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pizzeria_id', 'language_id'], 'required'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['pizzeria_id', 'language_id'], 'integer'],
            [['name', 'address', 'schedule'], 'string', 'max' => 255],
            [['pizzeria_id', 'language_id'], 'unique', 'targetAttribute' => ['pizzeria_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pizzeria_id' => 'ID',
            'language_id' => 'ID языка',
            'name' => 'Название',
            'address' => 'Адрес',
            'schedule' => 'График работы',
        ];
    }

    /**
     * @param string $pizzeriaId
     */
    public static function removeByPizzeriaId($pizzeriaId)
    {
        self::deleteAll(['pizzeria_id' => $pizzeriaId]);
    }
}
