<?php
/**
 * OrderHistoryController class file.
 */

namespace app\module\admin\module\order\controllers;

use Yii;
use app\module\admin\models\User;
use app\module\admin\module\order\models\Order;
use app\module\admin\module\order\models\OrderHistory;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderHistoryController implements the CRUD actions for OrderHistory model.
 */
class OrderHistoryController extends Controller
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
     * Creates a new OrderHistory model.
     *
     * @throws NotFoundHttpException if request is not Ajax
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isAjax) {
            $orderHistory = new OrderHistory();

            $orderHistory->load(Yii::$app->request->post());

            if (!empty($orderHistory->order_id)) {
                $order = Order::findOne(['order_id' => $orderHistory->order_id]);

                if (($order !== null) && $orderHistory->save()) {
                    $order->restaurant_id = $orderHistory->restaurant_id;
                    $order->save(false);

                    $order->updateStatus();
                }
            } else $orderHistory->save();
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

    /**
     * Deletes an existing OrderHistory model.
     *
     * @param integer $id model id
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception|\Throwable in case delete failed.
     * @throws \yii\db\StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     */
    public function actionDelete($id)
    {
        $orderHistory = $this->findModel($id);

        $order = Order::findOne(['order_id' => $orderHistory->order_id]);

        $orderHistory->delete();

        if (!empty($order)) {
            $order->updateStatus();
        }
    }

    /**
     * Finds the OrderHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id model id
     * @return OrderHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }
}
