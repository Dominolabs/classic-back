<?php

use yii\helpers\ArrayHelper;

$paramsFile = __DIR__ . '/params.inc';
$paramsFileContent = file_get_contents($paramsFile);
$paramsArray = unserialize(base64_decode($paramsFileContent));

$designFile = __DIR__ . '/design.inc';
$designFileContent = file_get_contents($designFile);
$designArray = unserialize(base64_decode($designFileContent));

return ArrayHelper::merge(ArrayHelper::merge($designArray, $paramsArray), [
    'user.passwordResetTokenExpire' => 3600,
    'webUrl' => 'https://classic.com.ua/uk/product/'
]);
