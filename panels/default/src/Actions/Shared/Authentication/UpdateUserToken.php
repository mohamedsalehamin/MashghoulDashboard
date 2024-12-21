<?php

namespace App\DefaultPanel\Actions\Shared\Authentication;

use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\User;
use App\UsersModule\Models\Manager;


class UpdateUserToken {
    use AsAction;

    public function handle($user): bool {
        $user->tokens()->delete();
        $user->update(['api_token' => $user->createToken("Tasawk:Token")->plainTextToken, 'phone_verified_at' => now()]);
        return true;
    }

}
