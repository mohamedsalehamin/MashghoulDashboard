<?php

namespace App\DefaultPanel\Actions\Customer;

use App\UsersModule\Models\User\Patient;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use App\CrmModule\Models\Customer;

class RegisterCustomer {
    use AsAction;


    /**
     * @throws Exception
     */
    public function handle($first_name,$last_name, $phone, $email, $password,$city_id,$gender,$dob,$device_token=null,$voip_token=null) {
        $patient = Patient::create([
            'name' => $first_name.' '.$last_name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'city_id'=>$city_id,
            'gender'=>$gender,
            'dob'=>$dob,
            'data'=>[
                'first_name'=>$first_name,
                'last_name'=>$last_name,
            ]

        ]);

        if ($device_token) {
            $patient->deviceTokens()->create(['token' => $device_token,'voip_token'=>$voip_token]);
        }
        return $patient;
    }

}

