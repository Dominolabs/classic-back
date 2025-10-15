<?php

namespace app\module\admin\models;

use app\components\ImageBehavior;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Model;

class SettingForm extends Model
{
    public $siteName;
    public $languageId;
    public $address = [];
    public $email;
    public $phone;
    public $pizzaConstructorBanner;
    public $pizzaConstructorBannerImage;
    public $pizzaConstructorBannerEn;
    public $pizzaConstructorBannerImageEn;

    public $mainPageId;
    public $mainPageMenuBannerId;

    public $publicOfferPageId;
    public $termsAndConditionsPageId;

    public $titlePrefix = [];
    public $titlePostfix = [];
    public $contactsMapSrc;
    public $contactsMapLat;
    public $contactsMapLng;
    public $contactsMapCoordinates;
    public $adminEmail;
    public $supportEmail;
    public $mobileAppIOS;
    public $mobileAppAndroid;

    public $availableOnlinePay;
    public $liqPayPublicKey;
    public $liqPayPrivateKey;
    public $liqPaySandbox;
    public $liqPaySendRRO;
    public $liqPayEmailsForCheck;

    public $webCamPassword;
    public $deliveryPrice;
    public $minKovelFreeDeliveryPrice;
    public $minAppVersion;
    public $notificationBirthDateBeforeWeek;
    public $notificationBirthDateBeforeDay;
    public $orderDefaultCityId;

    public $pizzaCategoryId;
    public $noodlesCategoryId;
    public $printerIp;
    public $printerPort;
    public $deliveryTime = [];
    public $selfPickingTime = [];
    public $deliveryDuration = [];
    public $deliveryPriceOutsideKovel = [];
    public $minCookingTime;
    public $minCookingTimeSelfPickup;
    public $maxCountMainIngredients;
    public $maxCountAdditionalIngredients;
    public $isSelfPickingActionAvailable;
    public $selfPickingActionDiscount;
    public $homeCityId;

    public $productBadges;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['languageId', 'siteName', 'adminEmail', 'supportEmail'], 'required'],
            [
                [
                    'languageId',
                    'mainPageId',
                    'mainPageMenuBannerId',
                    'liqPaySandbox',
                    'liqPaySendRRO',
                    'minAppVersion',
                    'orderDefaultCityId',
                    'pizzaCategoryId',
                    'noodlesCategoryId',
                    'minCookingTime',
                    'minCookingTimeSelfPickup',
                    'maxCountMainIngredients',
                    'maxCountAdditionalIngredients',
                    'homeCityId',
                    'publicOfferPageId',
                    'termsAndConditionsPageId',
                ],
                'integer'
            ],
            [
                [
                    'siteName',
                    'address',
                    'phone',
                    'mobileAppIOS',
                    'mobileAppAndroid',
                    'webCamPassword',
                    'availableOnlinePay',
                    'liqPayPublicKey',
                    'liqPayPrivateKey',
                    'notificationBirthDateBeforeWeek',
                    'notificationBirthDateBeforeDay',
                    'printerIp',
                    'printerPort',
                    'deliveryTime',
                    'deliveryDuration',
                    'deliveryPriceOutsideKovel',
                ],
                'string',
                'max' => 255
            ],
            [['email', 'adminEmail', 'supportEmail'], 'email'],
            /** @uses validateEmailList */
            [['liqPayEmailsForCheck'], 'validateEmailList'],
            [
                [
                    'address',
                    'titlePrefix',
                    'titlePostfix',
                    'contactsMapLat',
                    'contactsMapLng',
                    'deliveryTime',
                    'deliveryDuration',
                    'deliveryPriceOutsideKovel',
                    'selfPickingTime'
                ],
                'each',
                'rule' => ['string']
            ],
            [['contactsMapSrc', 'contactsMapCoordinates'], 'string', 'max' => 5000],
            [['deliveryPrice', 'minKovelFreeDeliveryPrice', 'selfPickingActionDiscount'], 'number'],
            ['productBadges', 'safe'],
            [['isSelfPickingActionAvailable'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'siteName' => 'Название сайта',
            'languageId' => 'Язык',
            'address' => 'Адрес',
            'email' => 'Email',
            'phone' => 'Телефон',
            'mainPageId' => 'Шаблон',
            'mainPageMenuBannerId' => 'Баннер меню',
            'titlePrefix' => 'Префикс для мета-тега Title',
            'titlePostfix' => 'Постфикс для мета-тега Title',
            'contactsMapSrc' => 'Карта',
            'contactsMapLat' => 'Широта',
            'contactsMapLng' => 'Долгота',
            'contactsMapCoordinates' => 'Координаты',
            'adminEmail' => 'Email администратора',
            'supportEmail' => 'Email поддержки',
            'mobileAppIOS' => 'IOS',
            'mobileAppAndroid' => 'Android',

            'availableOnlinePay' => 'Доступна онлайн оплата',
            'liqPayPublicKey' => 'Публичный ключ',
            'liqPayPrivateKey' => 'Приватный ключ',
            'liqPaySandbox' => 'Тестовый режим оплаты (режим разработки)',
            'liqPaySendRRO' => 'Отправлять список товаров в LiqPay (для РРО)',
            'liqPayEmailsForCheck' => 'Дополнительный email для отправки чеков',

            'webCamPassword' => 'Пароль доступа к веб-камерам',
            'deliveryPrice' => 'Стоимость доставки по умолчанию, грн',
            'minKovelFreeDeliveryPrice' => 'Минимальная стоимость заказа для бесплатной доставки в г. Ковель, грн',
            'minAppVersion' => 'Минимально допустимая версия приложения',
            'notificationBirthDateBeforeWeek' => 'Уведомление пользователя за неделю до Дня рождения',
            'notificationBirthDateBeforeDay' => 'Уведомление пользователя за день до Дня рождения',
            'orderDefaultCityId' => 'Город по умолчанию',

            'pizzaCategoryId' => 'Категория "Піца"',
            'noodlesCategoryId' => 'Категория "Локшина"',
            'printerIp' => 'IP адрес принтера',
            'printerPort' => 'Порт принтера',

            'pizzaConstructorBanner' => 'Банер пиццы конструктор',
            'pizzaConstructorBannerImage' => 'Банер пиццы контсруктор',
            'pizzaConstructorBannerEn' => 'Банер пиццы конструктор (Английский язык)',
            'pizzaConstructorBannerImageEn' => 'Банер пиццы контсруктор (Английский язык)',
            'deliveryTime' => 'График работы',
            'selfPickingTime' => 'Время доступное для самовывоза',
            'minCookingTime' => 'Минимальное время приготовления заказа (в минутах)',
            'minCookingTimeSelfPickup' => 'Минимальное время приготовления заказа (в минутах) для самовывоза',
            'maxCountMainIngredients' => 'Максимальное количество основных ингредиентов',
            'maxCountAdditionalIngredients' => 'Максимальное количество дополнительных ингредиентов',
            'homeCityId' => 'ID города Ковель из списка населенных пунктов',
            'deliveryDuration' => 'Продолжительность доставки',
            'deliveryPriceOutsideKovel' => 'Доставка за пределы города Ковель',

            'publicOfferPageId' => 'ID страницы "Договор публичной оферты"',
            'termsAndConditionsPageId' => 'ID страницы "Политика конфиденциальности"',

            'isSelfPickingActionAvailable' => 'Акция "Скидка при самовывозе"',
            'selfPickingActionDiscount' => 'Размер скидки по акции "Скидка при самовывозе" (%)',

            'productBadges' => 'Бейджи',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'pizzaConstructorBanner' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'setting',
            ],
            'pizzaConstructorBannerEn' => [
                'class' => ImageBehavior::class,
                'imageDirectory' => 'setting',
            ],
        ];
    }

    /**
     * @param string $field
     * @return bool
     */
    public function uploadFile($field)
    {
        $path = 'uploads/' . $this->{$field}->baseName . '.' . $this->{$field}->extension;
        $this->{$field}->saveAs($path);

        return $path;
    }

    /**
     * @param string $filename image filename
     * @param int $width image width in pixels
     * @param int $height image height in pixels
     * @param int $mode image resize mode (inset/outset)
     * @param int $quality image quality (0 - 100)
     * @return null|string
     */
    public static function getImageUrl(
        $filename,
        $width,
        $height,
        $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND,
        $quality = 100
    ) {
        return (new self())->resizeImage($filename, $width, $height, $mode, $quality);
    }

    /**
     * @return array
     */
    public static function getDeliveryTime ()
    {
        $key = array_key_first(Yii::$app->params['deliveryTime'] ?? []);

        if (is_null($key)) return ['', ''];
        $numbers = trim(preg_replace('/[^0-9: ]/', '',Yii::$app->params['deliveryTime'][$key]), ' ');
        $result = explode(' ', $numbers);
        $start = $end = null;
        foreach ($result as $key => $value)
        {
            if (!empty($value)) {
                if (empty($start)) $start = $value; else $end = $value;
            }
        }

        return [$start, $end];
    }


    /**
     * @return array
     */
    public static function getSelfPickingTime ()
    {
        $key = array_key_first(Yii::$app->params['selfPickingTime'] ?? []);

        if (is_null($key)) return ['', ''];

        $numbers = trim(preg_replace('/[^\d: ]/', '',Yii::$app->params['selfPickingTime'][$key]), ' ');
        $result = explode(' ', $numbers);
        return count($result) === 3 ? [$result[0], $result[2]] : ['', ''];
    }

    public function validateEmailList(): bool
    {
        $value = $this->liqPayEmailsForCheck;
        if (empty($value)) {
            return true;
        }
        $emails = preg_split('/, */',  $value);
        if ($emails) {
            if (!is_array($emails)) {
                $emails = [$emails];
            }
            foreach ($emails as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->addError('liqPayEmailsForCheck',  str_replace('{attribute}', $this->getAttributeLabel('liqPayEmailsForCheck'), Yii::t('yii',  '{attribute} is not a valid email address.')));
                }
            }
        }
        return empty($this->getErrors('liqPayEmailsForCheck'));
    }
}
