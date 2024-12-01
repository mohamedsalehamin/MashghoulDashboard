<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;


use App\DefaultPanel\Lib\SMS;
use App\UsersModule\Models\VerificationCode;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Lib\Utils;

class ForgetPassword {
    use AsAction;

    public function handle($user = null, $phone = null) {
        SendVerificationCode::run($user, $phone);
//        SMS::run($user->phone ?? $phone, "Tmoono OTP code: $code");

    }

}
