<?php

namespace app\commands;

use app\module\admin\models\User;
use Yii;
use yii\console\Controller;

class SendNotificationsController extends Controller
{
    /**
     * This command sends PUSH-notifications to user devices.
     */
    public function actionIndex()
    {
        $notificationBirthDateBeforeWeek = Yii::$app->params['notificationBirthDateBeforeWeek'];
        $notificationBirthDateBeforeDay = Yii::$app->params['notificationBirthDateBeforeDay'];

        // Send notifications for users before week of birthday
        if (!empty($notificationBirthDateBeforeWeek)) {
            $dateBeforeWeek = date('d.m', strtotime('+7 days'));
            $usersBirthdayBeforeWeek = User::findByBirthday($dateBeforeWeek);

            foreach ($usersBirthdayBeforeWeek as $user) {
                if (!empty($user['device_id']) && $user['device_id'] !== 'undefined' && $user['device_id'] !== 'null') {
                    User::sendExpoNotification($notificationBirthDateBeforeWeek, $user['device_id'], $user['user_id']);
                    User::addToNotificationsHistory('Classic', $notificationBirthDateBeforeWeek, [$user['user_id']]);
                }
            }
        }

        // Send notification for users before day of birthday
        if (!empty($notificationBirthDateBeforeDay)) {
            $dateBeforeDay = date('d.m', strtotime('+1 days'));
            $usersBirthdayBeforeDay = User::findByBirthday($dateBeforeDay);

            foreach ($usersBirthdayBeforeDay as $user) {
                if (!empty($user['device_id']) && $user['device_id'] !== 'undefined' && $user['device_id'] !== 'null') {
                    User::sendExpoNotification($notificationBirthDateBeforeDay, $user['device_id'], $user['user_id']);
                    User::addToNotificationsHistory('Classic', $notificationBirthDateBeforeDay, [$user['user_id']]);
                }
            }
        }
    }
}
