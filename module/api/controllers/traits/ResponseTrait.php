<?php

namespace app\module\api\controllers\traits;

use app\module\api\exceptions\NotFoundException;
use app\module\api\exceptions\ValidationException;
use Throwable;
use Yii;
use yii\web\Response;

trait ResponseTrait
{
    /**
     * @param Throwable|null $e
     * @return array
     */
    public static function apiErrorResponse(?Throwable $e = null)
    {
        $error = empty($e) ? [
            'message' => 'Something went wrong. Please try again or contact developers.'
        ] : [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
        return self::jsonResponse([
            'status' => 500,
            'errors' => $error
        ], 500);
    }

    /**
     * @param ValidationException $e
     * @return array
     */
    public static function apiValidationResponse(ValidationException $e)
    {
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
     * @return array
     */
    public static function handleException(Throwable $e)
    {
        if ($e instanceof ValidationException) return self::apiValidationResponse($e);
        if ($e instanceof NotFoundException) return self::apiNotFoundResponse($e->getMessage());
        return self::apiErrorResponse($e);
    }

    /**
     * @param array|null $data
     * @param int $status
     * @return array|null
     */
    public static function jsonResponse(?array $data = null, int $status = 200)
    {
        Yii::$app->response->statusCode = $status;
        Yii::$app->response->format = Response::FORMAT_JSON;
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
}