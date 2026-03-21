<?php

namespace App\Exceptions;

use Exception;
use Tasawk\Api\Facade\Api;
use Throwable;

class APIException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        protected ?string $errorCode = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function render($request)
    {
        $response = Api::isError(__($this->getMessage()))->setMessageAsValidationKey();

        if ($this->errorCode !== null) {
            $response->addAttribute('error_code', $this->errorCode);
        }

        return $response;
    }
}
