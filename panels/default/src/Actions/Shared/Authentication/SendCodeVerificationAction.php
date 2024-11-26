<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;

use App\Utilities\Lib\Action;
use Utilities;

class SendCodeVerificationAction extends Action {

    public function __construct($user,$phone=null) {
        $user->verificationCodes()->delete();
        $code = Utilities::generateActivationCode();
        $user->verificationCodes()->create(['phone' => $phone ?? $user->phone, "code" => $code]);

    }
}
