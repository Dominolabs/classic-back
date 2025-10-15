<?php

namespace app\module\admin\module\currency\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "tbl_currency".
 *
 * @property int $currency_id
 * @property string $title
 * @property string $code
 * @property string $symbol_left
 * @property string $symbol_right
 * @property string $decimal_place
 * @property string $value
 * @property int $status
 * @property int $updated_at
 */
class Currency extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_currency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'code', 'decimal_place', 'value', 'status'], 'required'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            [['value'], 'number'],
            [['updated_at'], 'integer'],
            [['title'], 'string', 'max' => 32],
            [['code'], 'string', 'max' => 3],
            [['symbol_left', 'symbol_right'], 'string', 'max' => 16],
            [['decimal_place', 'status'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'currency_id' => 'ID валюты',
            'title' => 'Название',
            'code' => 'Код',
            'symbol_left' => 'Символ слева',
            'symbol_right' => 'Символ справа',
            'decimal_place' => 'Кол-во знаков после запятой',
            'value' => 'Курс',
            'status' => 'Статус',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp'  => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
            ]
        ];
    }

    /**
     * Returns statuses list.
     *
     * @return array statuses list data
     */
    public static function getStatusesList()
    {
        return [
            self::STATUS_ACTIVE => 'Включено',
            self::STATUS_NOT_ACTIVE => 'Отключено'
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
        return isset($statuses[$status]) ? $statuses[$status] : 'Неопределено';
    }

    /**
     * Returns currencies list.
     *
     * @return array currencies list
     */
    public static function getList()
    {
        $key = __CLASS__ . '_currencies_list';

        $currencies = Yii::$app->cache->get($key);

        if ($currencies === false) {
            $currencies = [];
            $currencyModels = Currency::find()->all();

            /** @var Currency $currencyModel */
            foreach ($currencyModels as $currencyModel) {
                $currencies[$currencyModel->code] = array(
                    'currency_id' => $currencyModel->currency_id,
                    'title' => $currencyModel->title,
                    'code' => $currencyModel->code,
                    'symbol_left' => $currencyModel->symbol_left,
                    'symbol_right' => $currencyModel->symbol_right,
                    'decimal_place' => $currencyModel->decimal_place,
                    'value' => $currencyModel->value,
                    'status' => $currencyModel->status,
                    'updated_at' => $currencyModel->updated_at,
                );
            }

            Yii::$app->cache->set($key, $currencies);
        }

        return $currencies;
    }

    /**
     * Formats currency value.
     *
     * @param int $number number
     * @param string $currency currency code
     * @param string $value currency value
     * @param bool $format true to format currency
     * @return float|int|string result currency value
     */
    public static function format($number, $currency, $value = '', $format = true)
    {
        $currencies = self::getList();

        $symbolLeft = $currencies[$currency]['symbol_left'];
        $symbolRight = $currencies[$currency]['symbol_right'];
        $decimalPlace = $currencies[$currency]['decimal_place'];

        if (!$value) {
            $value = $currencies[$currency]['value'];
        }

        $amount = $value ? (float)$number * $value : (float)$number;

        $amount = round($amount, (int)$decimalPlace);

        if (!$format) {
            return $amount;
        }

        $string = '';

        if ($symbolLeft) {
            $string .= $symbolLeft;
        }

        $string .= number_format($amount, (int)$decimalPlace, '.', '');

        if ($symbolRight) {
            $string .= $symbolRight;
        }

        return $string;
    }

    /**
     * Returns default currency model.
     *
     * ToDo: Replace this hardcoded value to currency id from settings
     *
     * @return null|static currency model
     */
    public static function getDefault()
    {
        return self::findOne(['currency_id' => 1]);
    }
}
