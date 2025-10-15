<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\module\admin\models\User */

$resetLink = 'https://classic.com.ua/change-password?token=' . $user->password_reset_token;
?>

<?= Yii::t('api', 'Доброго дня') ?>.

<?= Yii::t('api', 'Ви або хтось інший запросили відновлення пароля до вашого профілю.') ?>

<?= Yii::t('api', 'Перейдіть за посиланням нижче, щоб скинути пароль:') ?>

<?= Html::a(Html::encode($resetLink), $resetLink) ?>

<?= Yii::t('api', 'Посилання активне протягом години.') ?>