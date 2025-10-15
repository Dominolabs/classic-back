<?php

/* @var $this yii\web\View */
/* @var $user app\module\admin\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['admin/reset-password', 'token' => $user->password_reset_token]);
?>
    Здравствуйте <?= $user->username ?>,

    Перейдите по ссылке ниже, чтобы сбросить пароль:

<?= $resetLink ?>