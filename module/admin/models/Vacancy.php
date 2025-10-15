<?php
/**
 * Banner model class file.
 */

namespace app\module\admin\models;


use yii\behaviors\TimestampBehavior;
use yii\db\Connection;
use yii\db\Query;
use Yii;
use yii\db\QueryInterface;

/**
 * This is the model class for table "{{%banner}}".
 *
 * @property int $vacancy_id
 * @property string $name
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property $vacancyDescription
 */


class Vacancy extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;


    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%vacancies}}';
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['status'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'vacancy_id' => 'ID вакансии',
            'vacancyName' => 'Название',
            'status'     => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Онволено'
        ];
    }


    public function fields()
    {
        return [
            'id' => 'vacancy_id',
            'vacancy_name' => function () {
                return $this->vacancyDescription->name ?? null;
            }
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }



     /**
     * Returns statuses list.
     *
     * @return array statuses list data
     */
    public static function getStatusesList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Активная',
            self::STATUS_NOT_ACTIVE => 'Не активная'
        ];
    }

    /**
     * Returns status name by specified status constant.
     *
     * @param integer $status status constant
     * @return mixed|string status name
     */
    public static function getStatusName($status)
    {
        $statuses = self::getStatusesList();
        return ($statuses[$status]) ?? 'Неопределено';
    }

    /**
     * Returns all banners.
     *
     * @param int $status banner status to filter. Defaults 'Active'
     * @return array banners data
     */
    public static function getAll($status = self::STATUS_ACTIVE): array
    {
        return (new Query())
            ->select('*')
            ->from(self::tableName())
            ->where(['status' => $status])
            ->all();
    }


    /**
     * @param integer $lang_id
     * @return array|null
     */
    public static function getListWithNames(int $lang_id): ?array
    {
        $vacancies = (new Query())
            ->select(['v.vacancy_id', 'd.name'])
            ->from(['v' => self::tableName()])
            ->leftJoin(['d' => 'tbl_vacancies_description'], 'v.vacancy_id = d.vacancy_id')
            ->where(['v.status' => self::STATUS_ACTIVE, 'language_id' => $lang_id])
            ->all();

        if(!empty($vacancies)){
            $result = [];
            foreach ($vacancies as $vacancy) {
                $result[$vacancy['vacancy_id']] = $vacancy['name'];
            }
            return $result;
        }
    }


       /**
     * Returns banner by banner id.
     *
     * @param int $bannerId banner id
     * @param int $status banner status to filter. Defaults 'Active'
     * @return array banner data
     */
    public static function getByBannerId($bannerId, $status = self::STATUS_ACTIVE): array
    {
        return (new Query())
            ->select(['banner_id', 'name', 'status'])
            ->from(self::tableName() . ' AS p')
            ->where(['banner_id' => $bannerId, 'status' => $status])
            ->one();
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVacancyDescription(): \yii\db\ActiveQuery
    {
        return $this->hasOne(VacancyDescription::class, ['vacancy_id' => 'vacancy_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }



    /**
     * @return mixed
     */
    public function getVacancyName()
    {
        return $this->vacancyDescription->name ?? null;
    }

}
