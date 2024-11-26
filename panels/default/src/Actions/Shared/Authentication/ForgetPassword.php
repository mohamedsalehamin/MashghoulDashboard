<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;


use App\DefaultPanel\Lib\SMS;
use App\UsersModule\Models\VerificationCode;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Lib\Utils;

class ForgetPassword {
    use AsAction;

    public function handle($user = null, $phone = null) {
        $code = Utils::randomOtpCode();
        VerificationCode::updateOrCreate(['phone' => $user->phone ?? $phone, "code" => $code], ['user_id' => $user?->id]);
        SMS::run($user->phone ?? $phone, "Tmoono OTP code: $code");

    }

}
