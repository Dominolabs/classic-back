<?php

namespace app\module\api\controllers;

use app\module\admin\models\Banner;
use app\module\admin\models\Classic;
use app\module\admin\models\SettingForm;
use app\module\admin\module\order\models\City;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;


class SettingController extends BaseApiController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors[] = [
            'class' => VerbFilter::class,
            'actions' => [
                'settings' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @param string $lang
     * @return array response data
     */
    public function actionSettings($lang): array
    {
        $exclude = ['productBadges'];
        Yii::$app->language = $lang;

        $file = __DIR__ . '/../../../config/params.inc';
        $content = file_get_contents($file);
        $data = unserialize(base64_decode($content));

        // Set appropriate translation
        foreach ($data as $key => $datum) {
            if (is_array($datum) && !in_array($key, $exclude)) {
                $data[$key] = [
                    'uk' => $datum[1],
                    'en' => $datum[2]
                ];
            }
            if ($key == 'productBadges' && !empty($data[$key])) {
                foreach ($data[$key] as &$badge) {
                    $badge['name']['uk'] = $badge['name'][1];
                    unset($badge['name'][1]);
                    $badge['name']['en'] = $badge['name'][2];
                    unset($badge['name'][2]);
                }
            }
        }

        // Remove secret params from response
        unset(
            $data['adminEmail'],
            $data['supportEmail'],
            $data['filterKey'],
            $data['liqPaySandbox'],
            $data['liqPayPublicKey'],
            $data['adminEmailVacancy'],
            $data['liqPayPrivateKey'],
            $data['pizzaConstructorBannerImage']
        );

        $data['isSelfPickingActionAvailable'] = (bool) $data['isSelfPickingActionAvailable'];
        $data['availableOnlinePay']         = (bool) $data['availableOnlinePay'];


        $classic = Classic::find()->where(['product_id' => 1])->one()->status;
        if  ($classic == 1) {
            $data['pizza_constructor_banners'] = [
                'uk' => $this->getImage($data['pizzaConstructorBanner'] ?? 'placeholder'),
                'en' => $this->getImage($data['pizzaConstructorBannerEn'] ?? 'placeholder'),
            ];
        } else {
            $data['pizza_constructor_banners'] = [
                null
            ];
        }



//        if(isset($data['pizzaConstructorBanner'])){
//            $data['pizzaConstructorBanner'] = $this->getImage($data['pizzaConstructorBanner']);
//        }
//
//        if(isset($data['pizzaConstructorBannerEn'])){
//            $data['pizzaConstructorBannerEn'] = $this->getImage($data['pizzaConstructorBannerEn']);
//        }


        // Additional Fields for Expo Application
//        $data['appSettings'] = [
//            'free_delivery_sum' => (int)$data['minKovelFreeDeliveryPrice'],
//            'delivery_price' => (int)$data['deliveryPrice'],
//            'countryside_delivery_price' => (int)$data['deliveryPrice'],
//            'delivery_duration' => 45,
//            'delivery_time' => [
//                'uk' => 'з 12:00 до 24:00',
//                'en' => 'from 12:00 to 24:00'
//            ],

//            'appVersion' => $data['minAppVersion'],
//            'terms_and_conditions_pages' => [
//                'public_offer' => 14,
//                'privacy_policy' => 15,
//            ],
//        ];
//        $data['contacts'] = [
//            'phone' => $data['phone'],
//            'email' => $data['email'],
//            'address' => $data['address'],
//        ];

        list($start, $end) = SettingForm::getDeliveryTime();
        list($start_self_picking, $end_self_picking) = SettingForm::getSelfPickingTime();

        $data['delivery'] = [
            'start' =>  $start,
            'end' =>  $end,
            'deliveryTime' => $data['deliveryTime'],
            'deliveryDuration' => $data['deliveryDuration'],
            'minKovelFreeDeliveryPrice' => $data['minKovelFreeDeliveryPrice'],
            'deliveryPriceOutsideKovel' => $data['deliveryPriceOutsideKovel']
        ];

        $data['self_picking'] = [
            'start' =>  $start_self_picking,
            'end' =>  $end_self_picking,
            'default' => $data['selfPickingTime'] ?? []
        ];

        $city = City::findOne($data['orderDefaultCityId']);

        if (!empty($city->delivery_price)) {
            $data['delivery']['kovelDeliveryPrice'] = $city->delivery_price;
        } else {
            $data['delivery']['kovelDeliveryPrice'] = 35.0000;
        }

        $data['pages'] = [
            'main' => [
                'mainPageId' => $data['mainPageId'],
                'banner' => Banner::findOne($data['mainPageMenuBannerId']),
            ]
        ];

        unset($data['mainPageId'], $data['mainPageMenuBannerId'], $data['deliveryTime'],
            $data['deliveryDuration'], $data['minKovelFreeDeliveryPrice'], $data['deliveryPriceOutsideKovel']
        );

        $data['terms_and_conditions_pages'] = [
            'public_offer' => $data['publicOfferPageId'],
            'privacy_policy' => $data['termsAndConditionsPageId'],
        ];

        unset($data['publicOfferPageId'], $data['termsAndConditionsPageId']);

        return [
            'status' => 'success',
            'data' => $data,
        ];
    }


    /**
     * @param $name
     * @return string
     */
    public function getImage($name): string
    {
        return isset($name) && $this->imageExists($name)
            ? BaseApiController::BASE_SITE_URL . 'image/setting/' . $name
            : BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
    }


    /**
     * @param $name
     * @return bool
     */
    protected function imageExists($name): bool
    {
        return file_exists(\Yii::$app->basePath . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR . 'setting' . DIRECTORY_SEPARATOR . $name);
    }
}
