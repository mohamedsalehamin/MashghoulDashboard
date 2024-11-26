<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;

use App\DefaultPanel\Lib\SMS;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Lib\NotificationMessageParser;
use App\DefaultPanel\Lib\Utils;
use App\DefaultPanel\Notifications\OTPCodeSentNotification;

class SendVerificationCode {
    use AsAction;

    public function handle($user, $phone = null) {
        $code = Utils::randomOtpCode();

        $user->verificationCodes()->create(['phone' => $phone ?? $user->phone, "code" => $code]);
        SMS::run($user->phone ?? $phone, "Tmoono OTP code: $code");


    }

}
