<?php

namespace app\module\admin\controllers;

use app\module\admin\models\NotificationForm;
use Yii;
use app\module\admin\models\User;
use app\module\admin\models\Module;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;

class NotificationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'editSortOrder' => [
                'class' => EditableColumnAction::class,
                'modelClass' => Module::class
            ]
        ]);
    }

    /**
     * {@inheritdoc}
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
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new NotificationForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->send()) {
                Yii::$app->session->setFlash('success', 'Уведомление успешно отправлено.');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось отправить уведомление.');
            }

            return $this->goBack();
        }

        Url::remember();

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
