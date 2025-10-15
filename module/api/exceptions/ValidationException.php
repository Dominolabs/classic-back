<?php

namespace app\module\api\exceptions;

use Throwable;

class ValidationException extends \Exception
{
    protected $errors;

    /**
     * AjaxValidationException constructor.
     * @param string $message
     * @param array $errors
     */
    public function __construct($errors = [], $message = '')
    {
        parent::__construct($message, 0, null);
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}