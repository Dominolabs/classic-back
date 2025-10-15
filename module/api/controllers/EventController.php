<?php

namespace app\module\api\controllers;

use app\components\ImageBehavior;
use app\module\admin\module\event\models\EventCategoryDescription;
use app\module\admin\module\event\models\Tag;
use app\module\admin\module\event\models\TagDescription;
use Yii;
use app\module\admin\module\event\models\Event;
use app\module\admin\module\event\models\EventCategory;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class EventController extends BaseApiController
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
                'categories' => ['GET'],
                'events' => ['GET'],
                'event' => ['GET'],
            ],
        ];

        return $behaviors;
    }


    /**
     * @param $lang
     * @return array
     */
    public function actionCategories($lang)
    {
        try {
            Yii::$app->language = $lang;
            $categories = EventCategory::find()
                ->where(['status' => EventCategory::STATUS_ACTIVE])
                ->orderBy(["sort_order" => "ASC"])
                ->all();

            return [
                'status' => 'success',
                'data' => [
                    'categories' => $categories
                ],
            ];
        } catch (\Throwable $exception) {
            return $this->errorResponseHandler($exception);
        }
    }


    /**
     * @param $lang
     * @return array
     */
    public function actionEvents($lang): array
    {
        Yii::$app->language = $lang;
        $get_data = Yii::$app->request->get();

        try {
            /*
             * If we have tag_id as a GET param - we need to fetch only events associated with this tag.
             * For this we will use hasMany relation which was set in Tag model
             */
            if (isset($get_data['tag_id'])) {
                $tag_id = $get_data['tag_id'];
            } elseif (isset($get_data['tag'])) { //for Petya
                $t = TagDescription::findOne(['name' => $get_data['tag']]);
                $tag_id = $t->tag_id ?? 0;
            }

            if (!empty($tag_id)) {
                $tag = Tag::findOne($tag_id);

                if(empty($tag)){
                    $message = "Tag with id {$tag_id} doesn't exists!";
                    return $this->notFoundResponseHandler($message);
                }

                $query = $tag->getEvents();
            }
            else {
                $query = Event::find();
            }


            if (isset($get_data['category_id'])) {
                $category_id = $get_data['category_id'];
                $category = (new \yii\db\Query())
                    ->select('*')
                    ->from('tbl_event_category_description')
                    ->where(['event_category_id' => $category_id])
                    ->one();

                //Check if category exists
                if (!$this->modelExists($category_id, EventCategory::class)) {
                    $message = "Category with id {$category_id} doesn't exists!";
                    return $this->notFoundResponseHandler($message);
                }
                if($category['name'] != 'Усі') {
                    $query->andWhere(['event_category_id' => $category_id]);
                }
            }

            $query = $query->andWhere(['status' => Event::STATUS_ACTIVE]);
            $pagination = $this->getPaginationObject($query, $get_data['page']);
            $events = $query->offset($pagination->offset)
                ->joinWith(['eventDescription' => function($query) {
                    $query->orderBy([
                        'UNIX_TIMESTAMP(STR_TO_DATE(date, \'%d.%m.%Y\'))' => SORT_DESC
                    ]);
                }])
                ->limit($pagination->limit)
                ->all();

            return $this->successEventsResponse($events, $pagination);
        } catch (\Throwable $exception) {
            return $this->errorResponseHandler($exception);
        }
    }


    /**
     * @param $lang
     * @return array
     */
    public function actionEvent($lang): array
    {
        Yii::$app->language = $lang;
        $get_data = Yii::$app->request->get();

        try {
            if (array_key_exists('event_id', $get_data)) {
                $event_id = $get_data['event_id'];
            } else {
                $event_id = $get_data['article_id'];
            }


            if (empty($event_id)) {
                throw new \Exception('Event ID was not provided');
            }

            $event = Event::findOne($event_id);

            if (empty($event)) {
                $message = "Event with ID {$event_id} doesn't exists";
                return $this->notFoundResponseHandler($message);
            }


            $last_events = Event::find()
                ->where(['status' => Event::STATUS_ACTIVE])
                ->orderBy('created_at DESC')
                ->limit(1)
                ->all();

            $last_events_ids = implode(',', array_column($last_events, 'event_id'));

            $related_events =  Event::find()
                ->where([
                    'status' => Event::STATUS_ACTIVE,
                    'event_category_id' => $event->event_category_id,
                ])
                ->andWhere("event_id NOT IN ({$last_events_ids})")
                ->orderBy('created_at DESC')
                ->limit(3)
                ->all();


            return [
                'status' => 'success',
                'data'   => [
                    'event'          => $event,
                    'last_events'    => $last_events,
                    'related_events' => $related_events
                ],
            ];
        } catch (\Throwable $exception) {
            return $this->errorResponseHandler($exception);
        }
    }


    /**
     * @param \Throwable $exception
     * @return array
     */
    protected function errorResponseHandler(\Throwable $exception): array
    {
        $error = $this->formErrorForLogging($exception);
        Yii::error($error, 'feedback');
        $response = [
            'status' => 'error',
            'error' => 'Internal server error',
            'message' => $exception->getMessage()
        ];
        Yii::$app->response->statusCode = 500;
        return $response;
    }


    /**
     * @param $model_id
     * @param $model_class
     * @return bool
     */
    protected function modelExists($model_id, $model_class): bool
    {
        $model = $model_class::findOne($model_id);
        return !empty($model) ? true : false;
    }


    /**
     * @param $message
     * @return array
     */
    protected function notFoundResponseHandler($message): array
    {
        $response = [
            'status' => 'error',
            'error' => $message
        ];
        Yii::$app->response->statusCode = 404;
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


    /**
     * @param $events
     * @param $pagination
     * @return array
     */
    public function successEventsResponse($events, $pagination): array
    {
        return [
            'status' => 'success',
            'data' => $events,
            'meta' => [
                'total_pages' => $pagination->getPageCount(),
                'current_page' => $pagination->getPage() + 1,
                'page_size' => $pagination->getPageSize(),
            ]
        ];
    }


    /**
     * @param $query
     * @param $page
     * @return Pagination
     */
    public function getPaginationObject($query, $page): Pagination
    {
        return new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => Yii::$app->request->get('page_size') ?? 10,
            'page' => $page - 1
        ]);
    }
}
