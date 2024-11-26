<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;

use Lorisleiva\Actions\Concerns\AsAction;


class SendOTPCodeAction {
    use AsAction;

    public function handle($user=null,$phone=null): void {
        ForgetPassword::run($user,$phone);

    }

}
