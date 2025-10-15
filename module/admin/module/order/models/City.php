<?php

namespace app\module\admin\module\order\models;

use app\module\admin\models\Language;
use app\module\api\controllers\BaseApiController;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * @property int $id
 * @property float $delivery_price
 * @property int $minimum_order
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 *
 * @property string $name
 * @property string $pb_id
 * @property CityDescription $cityDescription
 * @property CityDescription $cityDescriptionDefaultLanguage
 */
class City extends ActiveRecord
{
    public const STATUS_NOT_ACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'tbl_city';
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status', 'sort_order', 'delivery_price', 'minimum_order'], 'required'],
            [['delivery_price'], 'number'],
            [['pb_id'], 'string'],
            [['minimum_order', 'free_minimum_order', 'status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'delivery_price' => 'Стоимость доставки',
            'pb_id' => 'ID стоимости доставки в ПриватБанке (для РРО)',
            'minimum_order' => 'Минимальный заказ, грн',
            'free_minimum_order' => 'Сума для бесплатной доставки, грн',
            'cityName' => 'Название',
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }


    public function fields(): array
    {
        return [
            'id',
            'name',
            'delivery_price',
            'pb_id',
            'minimum_order',
            'free_minimum_order',
            'sort_order',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCityDescription(): ActiveQuery
    {
        return $this->hasOne(CityDescription::class, ['city_id' => 'id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCityDescriptionDefaultLanguage(): ActiveQuery
    {
        return $this->hasOne(CityDescription::class, ['city_id' => 'id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if (!empty($this->cityDescription->name)) {
            return $this->cityDescription->name;
        }

        if (!empty($this->cityDescriptionDefaultLanguage->name)) {
            return $this->cityDescriptionDefaultLanguage->name;
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getCityName()
    {
        if ($this->cityDescription !== null) {
            return $this->cityDescription->name;
        }
        return null;
    }

    /**
     * @return array
     */
    public static function getStatusesList()
    {
        return [
            self::STATUS_ACTIVE => 'Включено',
            self::STATUS_NOT_ACTIVE => 'Отключено'
        ];
    }

    /**
     * @param integer $status
     * @return mixed|string
     */
    public static function getStatusName($status)
    {
        $statuses = self::getStatusesList();

        return isset($statuses[$status]) ? $statuses[$status] : 'Неопределено';
    }

    /**
     * @param int $status
     * @param string $order
     * @param int $limit
     * @return array
     */
    public static function getAll($status = self::STATUS_ACTIVE, $order = 'c.sort_order ASC', $limit = null)
    {
        $query = (new Query())
            ->select('c.*, (CASE WHEN cd.name != "" THEN cd.name ELSE cd2.name END) as name')
            ->from(self::tableName() . ' AS c')
            ->leftJoin(CityDescription::tableName() . ' AS cd', 'c.id = cd.city_id AND cd.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(CityDescription::tableName() . ' AS cd2', 'c.id = cd2.city_id AND cd2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where(['c.status' => $status])
            ->groupBy('cd.city_id')
            ->orderBy($order)
            ->limit($limit);

        return $query->all();
    }

    /**
     * @param int $status
     * @return array
     */
    public static function getList($status = self::STATUS_ACTIVE)
    {
        $result = [];
        $cities = self::getAll($status);

        foreach ($cities as $city) {
            $result[$city['id']] = $city['name'];
        }

        return $result;
    }
}
