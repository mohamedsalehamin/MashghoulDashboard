<?php

namespace App\Exceptions;

use Exception;
use Tasawk\Api\Facade\Api;

class AccountNeedActivationException extends Exception {
    public function render($request) {
        return Api::isError(__("Your account need to be verified, SMS code sent"))->addAttribute('need_activation', 1);
    }

}
