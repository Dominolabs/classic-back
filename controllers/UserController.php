<?php


namespace app\controllers;

use app\module\admin\models\User;
use Throwable;
use Yii;
use yii\web\Controller;

class UserController extends Controller
{
    public function actionUnsubscribe()
    {
        try {
            /** @var User $user */
            $user = User::where('user_id', Yii::$app->request->get('user'))->one();

            if ($user) {
                $user->update([
                    'send_emails' => 0
                ]);
            }

            return $this->renderFile('@app/themes/default/user/success.php');
        } catch (Throwable $e) {
            return $this->renderFile( Yii::getAlias('@app/themes/default/user/error.php'), [
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actionFix()
    {
        Yii::$app->db->createCommand(
            "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))"
        )->execute();

        $mode = Yii::$app->db->createCommand("SELECT @@GLOBAL.sql_mode")->queryScalar();

        if (strpos($mode, 'ONLY_FULL_GROUP_BY') === false) {
            return "✅ ONLY_FULL_GROUP_BY успішно вимкнено! Поточний sql_mode: $mode";
        }

        return "❌ Не вдалося вимкнути ONLY_FULL_GROUP_BY. Поточний sql_mode: $mode";
    }
}