<?php

namespace App\DefaultPanel\Rules;

use App\UsersModule\Models\Users\Customer;
use App\UsersModule\Models\Users\Provider;
use Illuminate\Contracts\Validation\ValidationRule;

class ProviderRegistrationPhoneRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $phoneNumber = phone(number: $value);
        if (! $phoneNumber->isValid()) {
            $fail(__('validation.api.invalid_phone_format'));

            return;
        }

        $raw = $phoneNumber->getRawNumber();

        if (Provider::where('phone', $raw)->exists()) {
            $fail(__('validation.phone_exists_in_providers'));

            return;
        }

        if (Customer::where('phone', $raw)->exists()) {
            $fail(__('validation.phone_exists_in_customers'));

            return;
        }
    }
}
