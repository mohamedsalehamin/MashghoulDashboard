<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;

use Lorisleiva\Actions\Concerns\AsAction;
use Notification;
use App\DefaultPanel\Notifications\Customer\CustomerRegisteredNotification;
use App\Models\User;


class VerifyUserAccount {
    use AsAction;

    public function handle(User $user) {
        $user->update(['phone_verified_at' => now()]);
        UpdateUserToken::run($user);
        RemoveVerficationCodes::run($user);
//        Notification::send($user, new CustomerRegisteredNotification());


    }

}
