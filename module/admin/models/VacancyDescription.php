<?php
/**
 * Banner model class file.
 */

namespace app\module\admin\models;

use yii\db\Query;

/**
 * This is the model class for table "{{%banner}}".
 *
 * @property int $vacancy_id
 * @property int $language_id
 * @property string $name
 */


class VacancyDescription extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%vacancies_description}}';
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['vacancy_id', 'language_id'], 'integer'],
            [['name'], 'required', 'on' => 'language-is-system'],
            [['vacancy_id', 'language_id'], 'unique', 'targetAttribute' => ['vacancy_id', 'language_id']],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'vacancy_id' => 'ID вакансии',
            'language_id' => 'ID языка',
            'name'       => 'Название'
        ];
    }

    /**
     * Removes product descriptions by product id.
     *
     * @param string $vacancy_id vacancy id
     */
    public static function removeByVacancyId($vacancy_id): void
    {
        self::deleteAll(['vacancy_id' => $vacancy_id]);
    }
}
