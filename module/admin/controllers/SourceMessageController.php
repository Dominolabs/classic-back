<?php
/**
 * SourceMessageController class file.
 */

namespace app\module\admin\controllers;

use Yii;
use app\module\admin\models\User;
use app\module\admin\models\Language;
use app\module\admin\models\Message;
use app\module\admin\models\SourceMessage;
use app\module\admin\models\SourceMessageSearch;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SourceMessageController implements the CRUD actions for SourceMessage model.
 */
class SourceMessageController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            /** @var User $identity */
                            $identity = Yii::$app->user->identity;

                            return $identity->isUser || $identity->isAdminHotel;
                        },
                        'denyCallback' => function ($rule, $action) {
                            $this->redirect('/');
                        },
                    ], [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SourceMessage models.
     *
     * @return mixed index view
     */
    public function actionIndex()
    {
        $searchModel = new SourceMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new SourceMessage model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed create view or response object
     */
    public function actionCreate()
    {
        $model = new SourceMessage();

        $messages = [];

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $message = new Message();

            $messages[$language['language_id']] = $message;
        }

        if ($model->load(Yii::$app->request->post()) && Message::loadMultiple($messages, Yii::$app->request->post())) {
            $isValid = $model->validate();

            $isValid = Message::validateMultiple($messages, Yii::$app->request->post()) && $isValid;

            if ($isValid && $model->save(false)) {
                /** @var Message $message */
                foreach ($messages as $key => $message) {
                    $message->source_message_id = $model->source_message_id;
                    $message->language_id = $key;
                    $message->save(false);
                }
                return $this->goBack();
            }
        }

        return $this->render('create', [
            'model' => $model,
            'languages' => $languages,
            'messages' => $messages,
        ]);
    }

    /**
     * Updates an existing SourceMessage model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id SourceMessage id
     * @return mixed update view or response object
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $messages = [];

        $languages = Language::getAll(Language::STATUS_ACTIVE);

        foreach ($languages as $language) {
            $message = Message::findOne([
                'source_message_id' => $model->source_message_id,
                'language_id' => $language['language_id']
            ]);

            if (empty($message)) {
                $message = new Message();
                $message->source_message_id = $model->source_message_id;
                $message->language_id = $language['language_id'];
            }

            $messages[$language['language_id']] = $message;
        }

        if ($model->load(Yii::$app->request->post()) && Message::loadMultiple($messages, Yii::$app->request->post())) {
            $isValid = $model->validate();

            $isValid = Message::validateMultiple($messages, Yii::$app->request->post()) && $isValid;

            if ($isValid && $model->save(false)) {
                /** @var Message $message */
                foreach ($messages as $key => $message) {
                    $message->source_message_id = $model->source_message_id;
                    $message->language_id = $key;
                    $message->save(false);
                }
                return $this->goBack();
            }
        }

        return $this->render('update', [
            'model' => $model,
            'languages' => $languages,
            'messages' => $messages,
        ]);
    }

    /**
     * Deletes an existing SourceMessage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id SourceMessage id
     * @return mixed response object
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception|\Throwable in case delete failed.
     * @throws \yii\db\StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Message::removeBySourceMessageId($id);

        return $this->goBack();
    }

    /**
     * Flushes application cache.
     *
     * @return \yii\web\Response response object
     */
    public function actionFlushCache()
    {
        if (Yii::$app->cache->flush()) {
            Yii::$app->session->setFlash('success', 'Кеш успешно очищен.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось очистить кеш.');
        }

        return $this->goBack();
    }

    /**
     * Finds the SourceMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id model id
     * @return SourceMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SourceMessage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }
}
