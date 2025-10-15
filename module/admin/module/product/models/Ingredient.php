<?php

namespace app\module\admin\module\product\models;

use app\module\api\controllers\BaseApiController;
use app\module\api\module\viber\controllers\helpers\Helper;
use Imagine\Image\ManipulatorInterface;
use app\jobs\ImageCopiesJob;
use Yii;
use app\components\ImageBehavior;
use app\module\admin\models\Language;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * @property int $ingredient_id
 * @property int $price
 * @property string $image
 * @property int $category_id
 * @property int $show_in_constructor_main
 * @property int $show_in_constructor_additional
 * @property int $status
 * @property int $sort_order
 * @property int $created_at
 * @property int $updated_at
 * @property int $pb_id
 *
 * @property string $name
 * @property string $portionSize
 * @property string $imageFile
 * @property IngredientDescription $ingredientDescription
 * @property IngredientDescription $ingredientDescriptionDefaultLanguage
 */


class Ingredient extends ActiveRecord
{
    public const STATUS_NOT_ACTIVE = 0;
    public const STATUS_ACTIVE = 1;
    public const SHOW = 1;
    public const HIDE = 0;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'tbl_ingredient';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['category_id', 'status', 'sort_order', 'pb_id'], 'required'],
            [['sort_order', 'price', 'category_id', 'show_in_constructor_main', 'show_in_constructor_additional', 'created_at', 'updated_at'], 'integer'],
            [['image', 'pb_id'], 'string', 'max' => 255],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            ['show_in_constructor_main', 'in', 'range' => [self::SHOW, self::HIDE]],
            ['show_in_constructor_additional', 'in', 'range' => [self::SHOW, self::HIDE]],
            [
                ['imageFile'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg, gif, svg',
                'maxSize' => 1024 * 1024 * 50
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ingredient_id' => 'ID ингредиента',
            'pb_id' => 'ID товара в ПриватБанке (для РРО)',
            'image' => 'Изображение',
            'imageFile' => 'Изображение',
            'price' => 'Цена',
            'category_id' => 'Категория',
            'status' => 'Статус',
            'sort_order' => 'Порядок сортировки',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'portionSize' => 'Размер порции',
            'categoryName' => 'Категория',
            'name' => 'Название',
            'show_in_constructor_main' => 'Отображать в списке основных ингредиентов',
            'show_in_constructor_additional' => 'Отображать в списке дополнительных ингредиентов',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            'image' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'ingredient',
            ]
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id' => 'ingredient_id',
            'name',
            'portion_size' => 'portionSize',
            'price',
            'image' => static function($ingredient) {
                if (!empty($ingredient->image) && file_exists($ingredient->getImagePath() . DIRECTORY_SEPARATOR . $ingredient->image)) {
                    return BaseApiController::BASE_SITE_URL . 'image/ingredient/' . $ingredient->image;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'image_l' => static function ($model) {
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_l.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/ingredient/' . $model->image . '_l.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    return Helper::asset('image/ingredient/' . $model->image);
                }

                return Helper::asset('image/placeholder.png');
            },
            'image_m' => static function ($model) {
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_m.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/ingredient/' . $model->image . '_m.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    return Helper::asset('image/ingredient/' . $model->image);
                }

                return Helper::asset('image/placeholder.png');
            },
            'image_s' => static function ($model) {
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_s.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/ingredient/' . $model->image . '_s.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    return Helper::asset('image/ingredient/' . $model->image);
                }

                return Helper::asset('image/placeholder.png');
            },
            'image_xs' => static function ($model) {
                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image . '_xs.' . ImageBehavior::getExtension($model->image))) {
                    return Helper::asset( 'image/ingredient/' . $model->image . '_xs.' . ImageBehavior::getExtension($model->image));
                }

                if (!empty($model->image) && file_exists($model->getImagePath() . DIRECTORY_SEPARATOR . $model->image)) {
                    Yii::$app->queue->push(new ImageCopiesJob([
                        'file' => $model->getImagePath() . DIRECTORY_SEPARATOR . $model->image
                    ]));
                    return Helper::asset('image/ingredient/' . $model->image);
                }
                $model->image = '';
                $model->save(false);

                return Helper::asset('image/placeholder.png');
            },
            'image_preview' => static function($ingredient) {
                if (!empty($ingredient->image) && file_exists($ingredient->getImagePath() . DIRECTORY_SEPARATOR . $ingredient->image)) {
                    return BaseApiController::BASE_SITE_URL . trim($ingredient->resizeImage($ingredient->image, 300, 300), '/');
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'checked' => static function () {
                return 0;
            },
            'show_in_constructor_main' => static function ($ingredient) {
                return (bool) $ingredient->show_in_constructor_main;
            },
            'show_in_constructor_additional' => static function ($ingredient) {
                return (bool) $ingredient->show_in_constructor_additional;
            }
        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getIngredientDescription(): ActiveQuery
    {
        return $this->hasOne(IngredientDescription::class, ['ingredient_id' => 'ingredient_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * @return ActiveQuery
     */
    public function getIngredientDescriptionDefaultLanguage(): ActiveQuery
    {
        return $this->hasOne(IngredientDescription::class, ['ingredient_id' => 'ingredient_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if (!empty($this->ingredientDescription->name)) {
            return $this->ingredientDescription->name;
        }

        if (!empty($this->ingredientDescriptionDefaultLanguage->name)) {
            return $this->ingredientDescriptionDefaultLanguage->name;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getPortionSize(): string
    {
        if (!empty($this->ingredientDescription->portion_size)) {
            return $this->ingredientDescription->portion_size;
        }

        if (!empty($this->ingredientDescriptionDefaultLanguage->portion_size)) {
            return $this->ingredientDescriptionDefaultLanguage->portion_size;
        }

        return '';
    }

    /**
     * @return int|string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->ingredient_id;
    }

    /**
     * ActiveRelation to CategoryDescription model.
     *
     * @return \yii\db\ActiveQuery active query instance
     */
    public function getCategoryDescription()
    {
        return $this->hasOne(CategoryDescription::class, ['category_id' => 'category_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * Returns category name.
     *
     * @return mixed category name
     */
    public function getCategoryName()
    {
        $categoryName = $this->categoryDescription->name ?? '';

        $categoryPathName = (new Query())
            ->select('GROUP_CONCAT(cd.name ORDER BY level SEPARATOR " > ")')
            ->from(CategoryPath::tableName() . ' AS cp')
            ->leftJoin(CategoryDescription::tableName() . ' AS cd',
                'cp.path_id = cd.category_id AND cp.category_id != cp.path_id')
            ->where('cp.category_id = ' . $this->category_id . ' AND cd.language_id = ' . Language::getLanguageIdByCode(Yii::$app->language))
            ->groupBy('cp.category_id')
            ->scalar();

        return !empty($categoryPathName) ? ($categoryPathName . ' > ' . $categoryName) : $categoryName;
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
     * Returns status name by specified status constant.
     *
     * @param integer $status status constant
     * @return string status name
     */
    public static function getStatusName($status)
    {
        $statuses = self::getStatusesList();
        return $statuses[$status] ?? 'Неопределено';
    }

    /**
     * @param string $filename image filename
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @param int $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100). Defaults 100.
     * @return null|string image URL
     */
    public static function getImageUrl($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_INSET, $quality = 100)
    {
        return (new self())->resizeImage($filename, $width, $height, $mode, $quality);
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function getOriginalImagePath($filename)
    {
        return (new self())->getImagePath() . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * @param int $ingredientId
     * @return array|bool
     */
    public static function getIngredient($ingredientId)
    {
        return (new Query())
            ->select('*, (CASE WHEN id.name != "" THEN id.name ELSE id2.name END) as name, i.image, i.sort_order')
            ->distinct()
            ->from(self::tableName() . ' AS i')
            ->leftJoin(IngredientDescription::tableName() . ' AS id',
                'i.ingredient_id = id.ingredient_id AND id.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(IngredientDescription::tableName() . ' AS id2',
                'i.ingredient_id = id2.ingredient_id AND id2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where('i.ingredient_id = ' . $ingredientId . ' AND id.language_id = ' . Language::getLanguageIdByCode(Yii::$app->language) . ' AND i.status = ' . self::STATUS_ACTIVE)
            ->one();
    }

    /**
     * @return int|string
     */
    public static function getAllCount()
    {
        return self::find()->count();
    }

    /**
     * @param int|null $status
     * @return array
     */
    public static function getAll($status = null)
    {
        $query = (new Query())
            ->select('*, (CASE WHEN id.name != "" THEN id.name ELSE id2.name END) as name, i.image, i.sort_order')
            ->from(self::tableName() . ' AS i')
            ->leftJoin(IngredientDescription::tableName() . ' AS id',
                'i.ingredient_id = id.ingredient_id AND id.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(IngredientDescription::tableName() . ' AS id2',
                'i.ingredient_id = id2.ingredient_id AND id2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->where('id.language_id = ' . Language::getLanguageIdByCode(Yii::$app->language));

        if ($status !== null) {
            $query = $query->andWhere(['i.status' => $status]);
        }

        return $query->orderBy('i.sort_order ASC')->all();
    }

    /**
     * @param int $productId
     * @param int|null $status
     * @return array
     */
    public static function getAllByProductId($productId, $status = null)
    {
        $query = (new Query())
            ->select('*, (CASE WHEN id.name != "" THEN id.name ELSE id2.name END) as name,
                (CASE WHEN id.portion_size != "" THEN id.portion_size ELSE id2.portion_size END) as portion_size,
                (CASE WHEN (SELECT count(*) FROM tbl_product_ingredient WHERE ingredient_id = i.ingredient_id AND product_id = ' . $productId . ') THEN 1 ELSE 0 END) as checked,
                i.image, i.sort_order'
            )
            ->from(self::tableName() . ' AS i')
            ->leftJoin(IngredientDescription::tableName() . ' AS id',
                'i.ingredient_id = id.ingredient_id AND id.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->language))
            ->leftJoin(IngredientDescription::tableName() . ' AS id2',
                'i.ingredient_id = id2.ingredient_id AND id2.language_id = '
                . Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage()))
            ->andWhere('id.language_id = ' . Language::getLanguageIdByCode(Yii::$app->language));

        if ($status !== null) {
            $query = $query->andWhere(['i.status' => $status]);
        }

        $query->groupBy('i.ingredient_id');

        return $query->orderBy('i.sort_order ASC, id.name ASC, id2.name ASC')->all();
    }

    /**
     * @param int $status components status to filter. Defaults 'Active'
     * @return array languages list
     */
    public static function getList($status = self::STATUS_ACTIVE)
    {
        $result = [];
        $ingredients = self::getAll($status);

        foreach ($ingredients as $ingredient) {
            $result[$ingredient['ingredient_id']] = $ingredient;
        }

        return $result;
    }
}
