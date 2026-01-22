<?php

namespace App\DefaultPanel\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;
class IsUserNotCustomer implements Rule {
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

        if ($user && $user->toCustomer()->exists() && !$user->provider()->exists()) {
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
        return __("validation.phone_exists_in_customers");
    }
}
