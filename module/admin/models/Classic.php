<?php

namespace app\module\admin\models;

use app\module\admin\module\product\models\Category;
use app\module\api\controllers\BaseApiController;
use Imagine\Image\ManipulatorInterface;
use Yii;
use app\components\cart\CartPositionInterface;
use app\components\cart\CartPositionTrait;
use app\module\admin\module\currency\models\Currency;
use app\components\ImageBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @property int $product_id
 * @property string $image
 * @property string $price
 * @property string $price2
 * @property string $packaging_price
 * @property string $packaging_price2
 * @property string $pb_id
 * @property string $pb_big_id
 * @property int $properties
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property string $imageFile
 * @property ClassicDescription $productDescription
 * @property ClassicDescription $productDescriptionDefaultLanguage
 * @property string $name
 * @property string $description
 * @property string $slug
 */
class Classic extends ActiveRecord implements CartPositionInterface
{
    use CartPositionTrait;

    public const STATUS_NOT_ACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public const NO = 0;
    public const YES = 1;

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'tbl_classic';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['price', 'price2', 'packaging_price', 'packaging_price2'], 'number'],
            [['image'], 'string', 'max' => 255],
            [['image'], 'string', 'max' => 10000],
            [['properties', 'pb_id', 'pb_big_id'], 'string'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_NOT_ACTIVE, self::STATUS_ACTIVE]],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, svg', 'maxSize' => 1024 * 1024 * 10],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'product_id' => 'ID товара',
            'image' => 'Изображение',
            'pb_id' => 'ID товара в ПриватБанке (для РРО)',
            'pb_big_id' => 'ID товара (большая пицца) в ПриватБанке (для РРО)',
            'imageFile' => 'Изображение',
            'price' => 'Цена (Средняя пицца)',
            'price2' => 'Цена (Большая пицца)',
            'packaging_price' => 'Стоимость упаковки',
            'packaging_price2' => 'Стоимость упаковки (Большая пицца)',
            'properties' => 'Характеристики',
            'status' => 'Статус',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
            'productName' => 'Название',
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
                'imageDirectory' => 'product',
            ]
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'id' => 'product_id',
            'name',
            'description',
            'description_mobile_app' => static function($product){
                return strip_tags($product->description);
            },
            'image' => static function ($product) {
                if (!empty($product->image) && file_exists($product->getImagePath() . DIRECTORY_SEPARATOR . $product->image)) {
                    return BaseApiController::BASE_SITE_URL . 'image/product/' . $product->image;
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'image_preview' => static function ($product) {
                if (!empty($product->image) && file_exists($product->getImagePath() . DIRECTORY_SEPARATOR . $product->image)) {
                    return BaseApiController::BASE_SITE_URL . trim($product->resizeImage($product->image, 300, 300), '/');
                }

                return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
            },
            'category' => static function ($product) {
                return $product->category;
            },
            'price',
            'price2',
            'variants' => static function ($product) {
                $result = [];
                if(!empty((float)$product->price)){
                    $result[] = [
                        'size' => 1,
                        'price' => (float)$product->price,
                        'name' => Yii::t('product', 'Середня'),
                        'packaging_price' => (float)$product->packaging_price,
                    ];
                }
                if(!empty((float)$product->price2)){
                    $result[] = [
                        'size' => 2,
                        'price' => (float)$product->price2,
                        'name' => Yii::t('product', 'Велика'),
                        'packaging_price' => (float)$product->packaging_price2,
                    ];
                }
                return $result;
            },
            'properties' => static function ($product) {
                $properties = json_decode($product->properties, true);
                $propertiesData = [];

                foreach ($properties as $property) {
                    $propertiesData[] = [
                        'id' => $property['id'],
                        'property' => $property['property'][Yii::$app->language] ?? ($property['property'][Yii::$app->urlManager->getDefaultLanguage()] ?? ''),
                        'sort_order' => $property['sort_order']
                    ];
                }

                return $propertiesData;
            },
            'created_at',
            'updated_at',
            'slug',
            'is_constructor' => static function (){
                return true;
            },
            'type' => static function () {
                return 'classic';
            }
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProductDescription(): ActiveQuery
    {
        return $this->hasOne(ClassicDescription::class, ['product_id' => 'product_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductDescriptionDefaultLanguage(): ActiveQuery
    {
        return $this->hasOne(ClassicDescription::class, ['product_id' => 'product_id'])
            ->andOnCondition(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if (!empty($this->productDescription->name)) {
            return $this->productDescription->name;
        }

        if (!empty($this->productDescriptionDefaultLanguage->name)) {
            return $this->productDescriptionDefaultLanguage->name;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if (!empty($this->productDescription->description)) {
            return $this->productDescription->description;
        }

        if (!empty($this->productDescriptionDefaultLanguage->description)) {
            return $this->productDescriptionDefaultLanguage->description;
        }

        return '';
    }


    public function getCategory()
    {
        $pizzaCategoryId = (int)Yii::$app->params['pizzaCategoryId'] ?: null;
        return Category::findOne($pizzaCategoryId);
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        $seoUrl = SeoUrl::find()->where('query = \'classic_id=' . $this->product_id . '\'')->andWhere(['language_id' => Language::getLanguageIdByCode(Yii::$app->language)])->one();

        if ($seoUrl) {
            return $seoUrl->keyword;
        }

        $seoUrl = SeoUrl::find()->where('query = \'classic_id=' . $this->product_id . '\'')->andWhere(['language_id' => Language::getLanguageIdByCode(Yii::$app->urlManager->getDefaultLanguage())])->one();

        if (!empty($seoUrl)) {
            return $seoUrl->keyword;
        }

        return '';
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        if ((int) $this->getSize() === 1) {
            $price = $this->price;
        } else {
            $price = $this->price2;
        }

        if (!empty($this->getMainIngredients())) {
            foreach ($this->getMainIngredients() as $ingredient) {
                if ($this->type !== 'classic') {
                    $price += $ingredient['price'] * $ingredient['quantity'];
                }
            }
        }
        if (!empty($this->getAdditionalIngredients())) {
            foreach ($this->getAdditionalIngredients() as $ingredient) {
                $price += $ingredient['price'] * $ingredient['quantity'];
            }
        }

        return $price;
    }

    /**
     * Returns product id.
     * @return int product id
     */
    public function getId()
    {
        return $this->product_id . $this->size;
    }

    /**
     * @return array statuses list data
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
     * @return mixed|string
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
     * Formats product price according to specified format.
     *
     * You can use the following tokens to format price:
     *
     * {sl}     - currency symbol left
     * {value}  - currency value
     * {sr}     - currency symbol right
     *
     * Any other strings will be outputs as is.
     *
     * @param float $price
     * @param string $currency
     * @param bool|int $value
     * @param string $format
     * @param string $thousandsSeparator
     * @return mixed|string
     */
    public static function formatPrice($price, $currency, $value = false, $format = '{sl}{value}{sr}', $thousandsSeparator = ' ')
    {
        $currencies = Currency::getList();

        $symbolLeft = $currencies[$currency]['symbol_left'];
        $symbolRight = $currencies[$currency]['symbol_right'];
        $decimalPlace = $currencies[$currency]['decimal_place'];

        if (!$value) {
            $value = $currencies[$currency]['value'];
        }

        $amount = $value ? (float)$price * $value : (float)$price;

        $amount = round($amount, (int)$decimalPlace);

        $priceValue = number_format($amount, 0, '.', $thousandsSeparator);

        $string = $format;

        $string = str_replace('{sl}', $symbolLeft, $string);
        $string = str_replace('{value}', $priceValue, $string);
        $string = str_replace('{sr}', $symbolRight, $string);

        return $string;
    }
}
