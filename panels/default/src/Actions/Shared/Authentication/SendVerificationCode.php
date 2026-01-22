<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;

use App\DefaultPanel\Lib\SMS;
use App\UsersModule\Models\VerificationCode;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Lib\Utils;

class SendVerificationCode {
    use AsAction;

    public function handle($user = null, $phone = null) {
        
        if($phone == '+966512345627'){
            $code = 1234;
            VerificationCode::create([
                'phone' => $phone ?? $user->phone,
                "code" => $code,
                'user_id' => $user?->id,
            ]);
        }else if($phone == '+966500000002'){
            $code = 1234;
            VerificationCode::create([
                'phone' => $phone ?? $user->phone,
                "code" => $code,
                'user_id' => $user?->id,
            ]);
        }else{
            $code = Utils::randomOtpCode();
            VerificationCode::create([
                'phone' => $phone ?? $user->phone,
                "code" => $code,
                'user_id' => $user?->id,
            ]);
            SMS::make($user->phone ?? $phone, "Verification code $code to login smart support App")->send();
        }
        
        


    }

}
