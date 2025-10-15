<?php

namespace app\components;

use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class ErrorAction extends \yii\web\ErrorAction
{
    /**
     * @return string|void
     */
    public function run(): ?string
    {
        if (strpos(Yii::$app->request->pathInfo, 'api/') !== 0) {
            return parent::run();
        }


        $net = new ContentNegotiator([
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
            'languages' => [
                'en',
                'ru',
                'uk',
            ],
        ]);

        $net->negotiate();

        $response = Yii::$app->getResponse();
        $response->data = [
            'message' => $response->statusText,
            'status' => $response->statusCode,
    ];
        return $response->send();
    }
}
 
 