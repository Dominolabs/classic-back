<?php

namespace app\module\api\controllers;

use app\module\admin\models\Language;
use app\module\admin\models\Restaurant;
use app\module\admin\models\RestaurantCategory;
use app\module\admin\models\RestaurantDescription;
use app\module\admin\models\SeoUrl;
use app\module\admin\module\pizzeria\models\Pizzeria;
use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class RestaurantController extends BaseApiController
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
                'restaurants' => ['GET'],
                'restaurant' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @param string $lang
     * @return array
     */
    public function actionRestaurants($lang): array
    {
        Yii::$app->language = $lang;

        $categories = [];
        $restaurantCategories = RestaurantCategory::find()->all();

        /** @var RestaurantCategory $category */
        foreach ($restaurantCategories as $category) {
            $restaurantModels = Restaurant::find()->where(['restaurant_category_id' => $category->restaurant_category_id, 'status' => Restaurant::STATUS_ACTIVE])->orderBy(['sort_order' => SORT_ASC])->all();
            $restaurants = [];
            /** @var Restaurant $restaurant */
            foreach ($restaurantModels as $restaurant) {
                $restaurants[] = [
                    'id' => $restaurant->restaurant_id,
                    'title' => $restaurant->restaurantTitle,
                    'name' => $category->restaurantCategoryName . ' (' . $restaurant->restaurantTitle .')',
                    'image' => call_user_func(static function() use ($restaurant) {
                        if (!empty($restaurant->image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image)) {
                            return BaseApiController::BASE_SITE_URL . 'image/restaurant/' . $restaurant->image;
                        }

                        return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                    }),
                    'image_preview' => call_user_func(static function() use ($restaurant) {
                        if (!empty($restaurant->image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image)) {
                            return BaseApiController::BASE_SITE_URL . trim($restaurant->resizeImage($restaurant->image, 300, 300), '/');
                        }

                        return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                    }),
                    'image_transparent' => call_user_func(static function() use ($restaurant) {
                        if (!empty($restaurant->image_transparent) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image_transparent)) {
                            return BaseApiController::BASE_SITE_URL . 'image/restaurant/' . $restaurant->image_transparent;
                        }

                        return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                    }),
                    'image_transparent_preview' => call_user_func(static function() use ($restaurant) {
                        if (!empty($restaurant->image_transparent) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image_transparent)) {
                            return BaseApiController::BASE_SITE_URL . trim($restaurant->resizeImage($restaurant->image_transparent, 300, 300), '/');
                        }

                        return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                    }),
                    'background_image' => call_user_func(static function() use ($restaurant) {
                        if (!empty($restaurant->background_image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->background_image)) {
                            return BaseApiController::BASE_SITE_URL . 'image/restaurant/' . $restaurant->background_image;
                        }

                        return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                    }),
                    'background_image_preview' => call_user_func(static function() use ($restaurant) {
                        if (!empty($restaurant->background_image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->background_image)) {
                            return BaseApiController::BASE_SITE_URL . trim($restaurant->resizeImage($restaurant->background_image, 300, 300), '/');
                        }

                        return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                    }),
                    'slug' => $restaurant->slug,
                    'coordinates' => [
                        'lat' => $restaurant->lat,
                        'long' => $restaurant->long
                    ],
                    'gmap_link' => $restaurant->restaurantDescription->gmap ?? null,
                    'address' => $restaurant->restaurantAddress ?? null,
                    'phone' => $restaurant->restaurantPhone ?? null,
                    'sort_order' => $restaurant->sort_order,
                    'online_delivery' => $restaurant->online_delivery,
                    'schedule' => $restaurant->restaurantSchedule,
                    'self_picking' => $restaurant->self_picking
                ];
            }

            $categories[] = [
                'id' => $category->restaurant_category_id,
                'name' => $category->restaurantCategoryName,
                'slug' => $category->slug,
                'restaurants' => $restaurants
            ];
        }

        $restaurants = [];
        $restaurantModels = Restaurant::find()->where(['restaurant_category_id' => 0, 'status' => Restaurant::STATUS_ACTIVE])->orderBy(['sort_order' => SORT_ASC])->all();
        /** @var Restaurant $restaurant */
        foreach ($restaurantModels as $restaurant) {
            $restaurants[] = [
                'id' => $restaurant->restaurant_id,
                'title' => $restaurant->restaurantTitle,
                'name' => $restaurant->restaurantTitle . ($restaurant->restaurantAddress ? ' (' . $restaurant->restaurantAddress . ')' : null),
                'image' => call_user_func(static function() use ($restaurant) {
                    if (!empty($restaurant->image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image)) {
                        return BaseApiController::BASE_SITE_URL . 'image/restaurant/' . $restaurant->image;
                    }

                    return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                }),
                'image_preview' => call_user_func(static function() use ($restaurant) {
                    if (!empty($restaurant->image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image)) {
                        return BaseApiController::BASE_SITE_URL . trim($restaurant->resizeImage($restaurant->image, 300, 300), '/');
                    }

                    return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                }),
                'image_transparent' => call_user_func(static function() use ($restaurant) {
                    if (!empty($restaurant->image_transparent) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image_transparent)) {
                        return BaseApiController::BASE_SITE_URL . 'image/restaurant/' . $restaurant->image_transparent;
                    }

                    return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                }),
                'image_transparent_preview' => call_user_func(static function() use ($restaurant) {
                    if (!empty($restaurant->image_transparent) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->image_transparent)) {
                        return BaseApiController::BASE_SITE_URL . trim($restaurant->resizeImage($restaurant->image_transparent, 300, 300), '/');
                    }

                    return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                }),
                'background_image' => call_user_func(static function() use ($restaurant) {
                    if (!empty($restaurant->background_image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->background_image)) {
                        return BaseApiController::BASE_SITE_URL . 'image/restaurant/' . $restaurant->background_image;
                    }

                    return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                }),
                'background_image_preview' => call_user_func(static function() use ($restaurant) {
                    if (!empty($restaurant->background_image) && file_exists($restaurant->getImagePath() . DIRECTORY_SEPARATOR . $restaurant->background_image)) {
                        return BaseApiController::BASE_SITE_URL . trim($restaurant->resizeImage($restaurant->background_image, 300, 300), '/');
                    }

                    return BaseApiController::BASE_SITE_URL . 'image/placeholder.png';
                }),
                'slug' => $restaurant->slug,
                'coordinates' => [
                    'lat' => $restaurant->lat,
                    'long' => $restaurant->long
                ],
                'gmap_link' => $restaurant->restaurantDescription->gmap ?? null,
                'address' => $restaurant->restaurantAddress ?? null,
                'phone' => $restaurant->restaurantPhone ?? null,
                'sort_order' => $restaurant->sort_order,
                'schedule' => $restaurant->restaurantSchedule,
                'online_delivery' => $restaurant->online_delivery,
                'self_picking' => $restaurant->self_picking
            ];
        }
        return [
            'status' => 'success',
            'data' => [
                'categories' => $categories,
                'restaurants' => $restaurants
            ],
        ];
    }



    /**
     * @param $lang
     * @return array
     */
    public function actionRestaurantsForSelfPicking($lang)
    {
        try {
            Yii::$app->language = $lang;
            $pizzerias = Pizzeria::getAllForSelfPicking();
            $result = array_map(function($item){
                return [
                    'id' => $item->pizzeria_id,
                    'name' => $item->pizzeriaName
                ];
            }, $pizzerias);
            return [
                'status' => 'success',
                'data' => [
                    'pizzerias' => $result,
                ],
            ];
        } catch (\Throwable $exception) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => 'Something went wrong!'
            ];
        }
    }


    /**
     * @param string $lang
     * @param int $restaurant_id
     * @param string $slug
     * @return array
     */
    public function actionRestaurant($lang, $restaurant_id = null, $slug = null): array
    {
        Yii::$app->language = $lang;

        if ($slug) {
            $su = SeoUrl::find()->where(['keyword' => $slug])->one();
            $restaurant_id = preg_replace("/[^0-9]/", "", $su->query);
        }

        $restaurant = Restaurant::find()->where(['restaurant_id' => $restaurant_id, 'status' => Restaurant::STATUS_ACTIVE])->one();

        if ($restaurant) {
            return [
                'status' => 'success',
                'data' => $restaurant,
            ];
        }

        Yii::$app->response->statusCode = 404;

        return [
            'status' => 'error',
            'message' => 'Restaurant not found!'
        ];
    }
}
