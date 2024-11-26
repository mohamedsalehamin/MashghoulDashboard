<?php

namespace App\Exceptions;

use Exception;
use Tasawk\Api\Facade\Api;


class APIException extends Exception {
    public function render($request) {
        return Api::isError(__($this->getMessage()))->setMessageAsValidationKey();
    }

}
