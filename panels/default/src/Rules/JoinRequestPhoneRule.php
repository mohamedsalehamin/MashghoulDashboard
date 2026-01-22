<?php

namespace App\DefaultPanel\Rules;

use App\ContentModule\Models\JoinRequest;
use App\UsersModule\Models\Users\Customer;
use App\UsersModule\Models\Users\Provider;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class JoinRequestPhoneRule implements Rule
{
    private string $message;

    public function passes($attribute, $value): bool
    {
        $phoneNumber = phone(number: $value);
        if(!$phoneNumber->isValid()){
            $this->message = __("validation.api.invalid_phone_format");
            return false;
        }
        $existingJoinRequest = JoinRequest::where('phone', $phoneNumber->getRawNumber())
            ->where('status', 'pending')
            ->exists();
       
        
        if ($existingJoinRequest) {
            $this->message = __('validation.phone_exists_in_join_requests');
            return false;
        }

        // Check if phone exists in providers
        $existingProvider = Provider::where('phone', $phoneNumber->getRawNumber())->exists();
       
        
        if ($existingProvider) {
            $this->message = __('validation.phone_exists_in_providers');
            return false;
        }

        // Check if phone exists in customers
        $existingCustomer = Customer::where('phone', $phoneNumber->getRawNumber())->exists();
       
        
        if ($existingCustomer) {
            $this->message = __('validation.phone_exists_in_customers');
            return false;
        }

        return true;

    }

    public function message(): string
    {
        return $this->message;
    }
} 