<?php


namespace app\components;


use yii\helpers\BaseUrl;



class UrlHelper extends BaseUrl
{
    public static function toAbsolute ($uri = '') {
        return static::home('https') . $uri;
    }
}