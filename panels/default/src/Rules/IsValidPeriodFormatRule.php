<?php

namespace App\DefaultPanel\Rules;

use App\UsersModule\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class IsValidPeriodFormatRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */


    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        return count(explode(' - ', $value)) == 2 && Carbon::parse(explode('-', $value)[0])->isBefore(Carbon::parse(explode('-', $value)[1]));

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return __("validation.api.invalid_period");
    }
}
