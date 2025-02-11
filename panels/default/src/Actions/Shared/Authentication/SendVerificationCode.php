<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;

use App\DefaultPanel\Lib\SMS;
use App\UsersModule\Models\VerificationCode;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Lib\Utils;

class SendVerificationCode {
    use AsAction;

    public function handle($user = null, $phone = null) {
        $code = Utils::randomOtpCode();

        VerificationCode::create([
            'phone' => $phone ?? $user->phone,
            "code" => $code,
            'user_id' => $user?->id,
        ]);
        SMS::make($user->phone ?? $phone, "Login OTP code: $code")->send();


    }

}
