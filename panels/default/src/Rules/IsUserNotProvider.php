<?php

namespace App\DefaultPanel\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;
class IsUserNotProvider implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {

        $user = User::where('phone', $value)->first();

        if ($user && $user->provider()->exists() && !$user->toCustomer()->exists()) {
            return false;
        }
        return true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return __("validation.phone_exists_in_providers");
    }
}
