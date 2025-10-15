<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\module\admin\models\User */

$resetLink = 'https://classic.com.ua/change-password?token=' . $user->password_reset_token;
?>
<div class="password-reset">
    <p><?= Yii::t('api', 'Доброго дня') ?>.</p>

    <p><?= Yii::t('api', 'Ви або хтось інший запросили відновлення пароля до вашого профілю.') ?></p>

    <p><?= Yii::t('api', 'Перейдіть за посиланням нижче, щоб скинути пароль:') ?>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>

    <p><?= Yii::t('api', 'Посилання активне протягом години.') ?>
</div>