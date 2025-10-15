<?php


namespace app\module\api\module\viber\controllers\helpers;


use Yii;
use yii\i18n\I18N;

class T extends I18N
{
    /**
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        try {
            return (new static)->translate($category, $message, $params, $language ?: Yii::$app->language);
        } catch (\Throwable $e){
            Helper::log($message, 't');
            return $message;
        }
    }

    public function init()
    {
        parent::init();
        $this->translations['*'] = [
            'class' => 'app\components\AppMessageSource',
            'sourceLanguage' => 'ru-RU',
        ];
    }
}