<?php

namespace App\DefaultPanel\Actions\Customer;

use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserToken;
use App\UsersModule\Models\User\Patient;
use App\UsersModule\Models\Users\Customer;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterCustomer {
    use AsAction;


    /**
     * @throws Exception
     */
    public function handle($first_name, $last_name, $phone, $city_id, $gender, $email = null, $dob = null, $device_token = null, $voip_token = null) {
        $customer = Customer::create([
            'name' => $first_name . ' ' . $last_name,
            'email' => $email,
            'phone' => $phone,
            'password' => '$$password$$',
            'city_id' => $city_id,
            'gender' => $gender,
            'dob' => $dob,
            'data' => [
                'first_name' => $first_name,
                'last_name' => $last_name,
            ]

        ]);
        UpdateUserToken::run($customer);
        if ($device_token) {
            $customer->deviceTokens()->create(['token' => $device_token, 'voip_token' => $voip_token]);
        }
        return $customer;
    }

}

