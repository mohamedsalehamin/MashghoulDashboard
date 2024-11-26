<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\User;

class RemoveVerficationCodes {
    use AsAction;

    public function handle(User $user) {
        return $user->verificationCodes()->delete();
    }

}
