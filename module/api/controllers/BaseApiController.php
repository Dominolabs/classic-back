<?php

namespace app\module\api\controllers;

use Yii;
use yii\base\Action;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class BaseApiController extends Controller
{
    public const BASE_SITE_URL = 'https://classic.devseonet.com/';

    /**
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        return ArrayHelper::merge($behaviors, [
            'corsFilter'  => [
                'class' => Cors::class,
            ],
            [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
        ]);
    }

    /**
     * @param Action $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        $language = Yii::$app->request->get('lang');

        if ($language !== null) {
            Yii::$app->language = $language;
        }

        return parent::beforeAction($action);
    }

    /**
     * @param Action $action
     * @param mixed $result
     * @return mixed
     */
    public function afterAction($action, $result)
    {
        Yii::info(Yii::$app->request->absoluteUrl, 'api');

        return parent::afterAction($action, $result);
    }
}
