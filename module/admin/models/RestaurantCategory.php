<?php

namespace app\module\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property int $restaurant_category_id
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 *
 * @property $restaurantCategoryName
 * @property RestaurantCategoryDescription $restaurantCategoryDescription
 * @property RestaurantCategoryDescription $restaurantCategoryDescriptionDefaultLanguage
 * @property string $slug
 */
class RestaurantCategory extends ActiveRecord
{
    public const STATUS_NOT_ACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'tbl_restaurant_category';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status', 'sort_order'], 'required'],
            [['status', 'sort_order', 'created_at', 'updated_at'], 'integer'],
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
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'restaurantCategoryName' => 'Название'
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getRestaurantCategoryDescription(): ActiveQuery
    {
        return $this->hasOne(RestaurantCategoryDescription::class, ['restaurant_category_id' => 'restaurant_category_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * @return ActiveQuery active query instance
     */
    public function getRestaurantCategoryDescriptionDefaultLanguage(): ActiveQuery
    {
        return $this->hasOne(RestaurantCategoryDescription::class, ['restaurant_category_id' => 'restaurant_category_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())]);
    }

    /**
     * @return string
     */
    public function getRestaurantCategoryName(): string
    {
        if (!empty($this->restaurantCategoryDescription->name)) {
            return $this->restaurantCategoryDescription->name;
        }

        if (!empty($this->restaurantCategoryDescriptionDefaultLanguage->name)) {
            return $this->restaurantCategoryDescriptionDefaultLanguage->name;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        $seoUrl = SeoUrl::find()->where('query = \'restaurant_category_id=' . $this->restaurant_category_id . '\'')->andWhere(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)])->one();

        if ($seoUrl) {
            return $seoUrl->keyword;
        }

        $seoUrl = SeoUrl::find()->where('query = \'restaurant_category_id=' . $this->restaurant_category_id . '\'')->andWhere(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())])->one();

        if (!empty($seoUrl)) {
            return $seoUrl->keyword;
        }

        return '';
    }

    /**
     * @return array
     */
    public static function getStatusesList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Включено',
            self::STATUS_NOT_ACTIVE => 'Отключено'
        ];
    }

    /**
     * @param integer $status
     * @return string
     */
    public static function getStatusName($status): string
    {
        $statuses = self::getStatusesList();
        return $statuses[$status] ?? 'Неопределено';
    }

    /**
     * @param int $status
     * @return array
     */
    public static function getList($status = self::STATUS_ACTIVE): array
    {
        $result = [];

        $restaurantCategories = self::find()->where(['status' => $status])->orderBy('sort_order ASC')->all();

        /** @var RestaurantCategory $restaurantCategory */
        foreach ($restaurantCategories as $restaurantCategory) {
            $result[$restaurantCategory['restaurant_category_id']] = $restaurantCategory->restaurantCategoryName;
        }

        return $result;
    }
}
