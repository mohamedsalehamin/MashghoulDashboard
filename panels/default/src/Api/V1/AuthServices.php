<?php

namespace App\DefaultPanel\Api\V1;

use App\DefaultPanel\Actions\Customer\RegisterCustomer;
use App\DefaultPanel\Actions\Shared\Authentication\RemoveVerficationCodes;
use App\DefaultPanel\Actions\Shared\Authentication\SendVerificationCode;
use App\DefaultPanel\Actions\Shared\Authentication\VerifyUserAccount;
use App\DefaultPanel\Lib\Utils;
use App\DefaultPanel\Notifications\Customer\CustomerRegisteredNotification;
use App\DefaultPanel\Requests\Api\Auth\CodeConfirmRequest;
use App\DefaultPanel\Requests\Api\Auth\LoginRequest;
use App\DefaultPanel\Requests\Api\Auth\RegisterCustomerRequest;
use App\DefaultPanel\Requests\Api\Auth\SendOTPRequest;
use App\DefaultPanel\Requests\Api\Auth\VerifyAccountRequest;
use App\DefaultPanel\Resources\Api\CustomerResource;
use App\DefaultPanel\Resources\Api\PatientResource;
use App\Exceptions\APIException;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Tasawk\Api\Core;
use Tasawk\Api\Facade\Api;

class AuthServices {

    /**
     * @throws APIException
     */
    public function login(LoginRequest $request) {


        $user = User::where('phone', $request->get('phone'))->first();
        SendVerificationCode::run(phone: $request->get('phone'), user: $user);
        return Api::isOk(__("SMS Code sent"))->addAttribute('registered', (bool)$user);
    }

    public function verifySMSCode(CodeConfirmRequest $request): Core {
        return Api::isOk(__("Correct Verification code"));

    }

    public function register(RegisterCustomerRequest $request) {
        $customer = RegisterCustomer::run(...$request->only("first_name", 'last_name', 'gender', 'phone', 'city_id', 'email', 'dob','avatar'));
        $customer->refresh();
        RemoveVerficationCodes::run($customer);
//        Notification::send(Utils::getAdministrationUsers(), new CustomerRegisteredNotification($customer));
        return Api::isOk(__("Customer info"), CustomerResource::make($customer));
    }

    public function verify(VerifyAccountRequest $request) {
        VerifyUserAccount::run($request->currentUser());
        return Api::isOk(__("Verified,User information"))->setData(new CustomerResource($request->currentUser()));

    }

    public function sendOTP(SendOTPRequest $request): Core {
        SendVerificationCode::run(phone: $request->get('phone'));
        return Api::isOk(__("OTP sent"))->setData([]);

    }


}
