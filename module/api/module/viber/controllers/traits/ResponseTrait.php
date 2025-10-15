<?php

namespace app\module\api\module\viber\controllers\traits;

use app\module\api\module\viber\controllers\helpers\Helper;
use Throwable;
use app\module\api\module\viber\exceptions\NotFoundException;
use app\module\api\module\viber\exceptions\ValidationException;
use Yii;
use yii\web\Response;

trait ResponseTrait
{
    public static $log_category = null;

    /**
     * @param Throwable|null $e
     * @param bool $with_log
     * @return array
     */
    public static function apiErrorResponse(?Throwable $e = null, $with_log = false)
    {
        $error = empty($e) ? [
            'message' => 'Something went wrong. Please try again or contact developers.'
        ] : [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
        if ($with_log) self::log($e);
        return self::jsonResponse([
            'status' => 500,
            'errors' => $error
        ], 500);
    }

    /**
     * @param ValidationException $e
     * @param bool $with_log
     * @return array
     */
    public static function apiValidationResponse(ValidationException $e, $with_log = false)
    {
        if ($with_log) self::log($e);
        return self::jsonResponse([
            'status' => 422,
            'errors' => $e->getErrors()
        ], 422);
    }

    /**
     * @param string|null $message
     * @return array
     */
    public static function apiNotFoundResponse(?string $message = null)
    {
        return self::jsonResponse([
            'status' => 404,
            'errors' => $message ?: 'Data not found.'
        ], 404);
    }

    /**
     * @param Throwable $e
     * @param bool $with_log
     * @return array
     */
    public static function handleException(Throwable $e, $with_log = false)
    {
        if ($e instanceof ValidationException) return self::apiValidationResponse($e, $with_log);
        if ($e instanceof NotFoundException) return self::apiNotFoundResponse($e->getMessage());
        return self::apiErrorResponse($e, $with_log);
    }

    /**
     * @param array|null $data
     * @param int $status
     * @return array|null
     */
    public static function jsonResponse(?array $data = null, int $status = 200)
    {
        if ($status !== 200)  Helper::log($data, 'viber');
        try {
            Yii::$app->response->statusCode = $status;
            Yii::$app->response->format = Response::FORMAT_JSON;
        } catch (Throwable $e) {
        }
        if (is_null($data))
            $data = [
                'status' => $status,
                'message' => 'OK'
            ];
        return $data;
    }

    /**
     * @param \Throwable $exception
     * @return array
     */
    protected function formErrorForLogging(\Throwable $exception): array
    {
        return [
            'url' => Yii::$app->request->absoluteUrl,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage()
        ];
    }

    /**
     * @param Throwable $e
     */
    public static function log(Throwable $e)
    {
        Yii::info([
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], self::$log_category);
    }

    /**
     * @return array|null
     */
    public static function letterSent()
    {
        return self::jsonResponse([
            'status' => 200,
            'message' => 'Letter was sent successfully.'
        ], 200);
    }
}