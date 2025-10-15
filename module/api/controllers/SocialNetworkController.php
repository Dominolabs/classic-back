<?php

namespace app\module\api\controllers;

use Yii;
use app\module\admin\models\SocialNetwork;
use app\module\admin\models\SocialNetworkCategory;
use yii\filters\VerbFilter;

class SocialNetworkController extends BaseApiController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors[] = [
            'class' => VerbFilter::class,
            'actions' => [
                'categories' => ['GET'],
                'social-networks' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Returns social network categories list.
     *
     * @param string $lang language code
     * @return array response data
     */
    public function actionCategories($lang)
    {
        Yii::$app->language = $lang;

        return [
            'status' => 'success',
            'data' => SocialNetworkCategory::getAll()
        ];
    }

    /**
     * Returns events list.
     *
     * @param string $lang language code
     * @param int|null $social_network_category_id social network category id
     * @return array response data
     */
    public function actionSocialNetworks($lang, $social_network_category_id = null)
    {
        Yii::$app->language = $lang;

        if ($social_network_category_id == null) {
            $data = SocialNetwork::getAll();
        } else {
            $data = SocialNetwork::getBySocialNetworkCategoryId($social_network_category_id);
        }

        return [
            'status' => 'success',
            'data' => $data
        ];
    }
}
