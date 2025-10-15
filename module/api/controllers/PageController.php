<?php

namespace app\module\api\controllers;

use app\components\ImageBehavior;
use app\models\WebcamForm;
use app\module\admin\models\Banner;
use app\module\admin\models\Vacancy;
use app\module\admin\module\hotelservice\models\Hotelservice;
use app\module\admin\module\room\models\Room;
use app\module\admin\module\team\models\Team;
use app\module\admin\module\event\models\Event;
use app\module\admin\module\gallery\models\Album;
use app\module\admin\module\tariff\models\Tariff;
use app\module\admin\models\BannerImage;
use app\module\admin\models\Language;
use app\module\admin\models\Page;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\module\api\presenters\PagePresenter;



class PageController extends BaseApiController
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => VerbFilter::class,
            'actions' => [
                'one' => ['GET'],
                'all' => ['GET'],
            ],
        ];
        return $behaviors;
    }

    /**
     * Returns pages list.
     * @param string $lang language code
     * @return array response data
     */
    public function actionAll($lang)
    {
        try {
            Yii::$app->language = $lang;
            $pages = Page::getTitlesAndIdsList();
            $provider = new ArrayDataProvider([
                'allModels' => $pages
            ]);
            $pages = $provider->getModels();

            return [
                'status' => 'success',
                'data' => [
                    'pages' => $pages
                ]
            ];
        } catch (\Throwable $exception) {
            return $this->errorResponseHandler($exception);
        }
    }


    /**
     * @param $lang
     * @param bool $page_id
     * @param string $slug
     * @return array
     */
    public function actionOne($lang, $page_id = false, $slug = false)
    {
        try {
            Yii::$app->language = $lang;
            $language_id = Language::getLanguageIdByCode(Yii::$app->language);

            if ($page_id) {
                $page = Page::getByIdAndLanguageId($page_id, $language_id);
            } else {
                $page = Page::getBySlugAndLanguageId($slug, $language_id);
            }

            if(empty($page)){
                $response = [
                    'status' => 'error',
                    'error' => 'Page no found',
                ];
                Yii::$app->response->statusCode = 404;
                return $response;
            }

            $for_mobile_app = Yii::$app->request->get('is_mobile') ?? false;
            $page_presenter = new PagePresenter($page, $for_mobile_app);

            return [
                'status' => 'success',
                'page' => $page_presenter->getResource()
            ];
        } catch (\Throwable $exception) {
            return [$this->formErrorForLogging($exception)];
        }
    }



    /**
     * @param \Throwable $exception
     * @return array
     */
    protected function errorResponseHandler(\Throwable $exception): array
    {
        $error = $this->formErrorForLogging($exception);
        Yii::error($error, 'api');
        $response = [
            'status' => 'error',
            'error' => 'Internal server error',
            'message' => $exception->getMessage()
        ];
        Yii::$app->response->statusCode = 500;
        return $response;
    }


    /**
     * @param \Throwable $exception
     * @return array
     */
    protected function formErrorForLogging(\Throwable $exception): array
    {
        return [
            'url' => Yii::$app->request->absoluteUrl,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage()
        ];
    }
}
