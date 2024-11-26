<?php

namespace App\DefaultPanel\Api\V1;

use App\DefaultPanel\Actions\Customer\CustomerHasRightsToLogin;
use App\DefaultPanel\Actions\Customer\RegisterCustomer;
use App\DefaultPanel\Actions\Shared\Authentication\ForgetPassword;
use App\DefaultPanel\Actions\Shared\Authentication\SendOTPCodeAction;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserPassword;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserToken;
use App\DefaultPanel\Actions\Shared\Authentication\VerifyUserAccount;
use App\DefaultPanel\Requests\Api\Auth\CodeConfirmRequest;
use App\DefaultPanel\Requests\Api\Auth\ForgetPasswordRequest;
use App\DefaultPanel\Requests\Api\Auth\LoginRequest;
use App\DefaultPanel\Requests\Api\Auth\PreStepRegisterCustomerRequest;
use App\DefaultPanel\Requests\Api\Auth\RegisterCustomerRequest;
use App\DefaultPanel\Requests\Api\Auth\ResetPasswordRequest;
use App\DefaultPanel\Requests\Api\Auth\SendOTPRequest;
use App\DefaultPanel\Requests\Api\Auth\UpdateHealthDataRequest;
use App\DefaultPanel\Requests\Api\Auth\VerifyAccountRequest;
use App\DefaultPanel\Resources\Api\PatientDataResource;
use App\DefaultPanel\Resources\Api\PatientResource;
use App\Exceptions\APIException;
use Tasawk\Api\Core;
use Tasawk\Api\Facade\Api;

class AuthServices {

    /**
     * @throws APIException
     */
    public function login(LoginRequest $request) {
        $request->authenticated();

        UpdateUserToken::run(patient());

        CustomerHasRightsToLogin::run();


        return Api::isOk('Customer information')->setData(new PatientResource(patient()));
    }


    public function verifySMSCode(CodeConfirmRequest $request): Core {
        return Api::isOk('Correct Verification code');

    }

    public function register(RegisterCustomerRequest $request): Core {

        $patient = RegisterCustomer::run(...$request->only("first_name", "last_name", "dob", 'email', 'password', 'phone', 'gender', 'city_id', 'device_token','voip_token'));
        if ($request->hasFile("avatar")) {
            auth()->user()->clearMediaCollection();
            auth()->user()->addMediaFromRequest("avatar")->toMediaCollection();
        }
        UpdateUserToken::run($patient);



        return Api::isOk(__("Patient data"), PatientResource::make($patient));
    }

    public function preStepRegister(PreStepRegisterCustomerRequest $request): Core {
        SendOTPCodeAction::run(phone: $request->get('phone'));
        return Api::isOk("OTP Code sent to your phone number");

    }

    public function verify(VerifyAccountRequest $request): Core {

        VerifyUserAccount::run($request->currentUser());
        return Api::isOk(__("Verified,User information"))->setData(new PatientResource($request->currentUser()));

    }

    public function forgetPassword(ForgetPasswordRequest $request): Core {

        ForgetPassword::run($request->currentUser());
        return Api::isOk(__("OTP Code sent"));

    }

    public function sentOtp(SendOTPRequest $request): Core {
        ForgetPassword::run(phone: $request->get('phone'));
        return Api::isOk(__("OTP Code sent"));
    }

    public function resetPassword(ResetPasswordRequest $request): Core {
        UpdateUserPassword::run($request->currentUser(), $request->get('password'));
        return Api::isOk(__("User information"))->setData(new PatientResource($request->currentUser()));

    }

    public function updateHealthData(UpdateHealthDataRequest $request): Core {
        if ($request->get('health_data')) {
            patient()->healthData()->updateOrCreate(['patient_id' => patient()->id], $request->get('health_data', []));
        }

        patient()->chronicDiseases()->syncWithoutDetaching($request->get('chronic_diseases_ids', []));

        foreach ($request->analysis??[] as $analysis) {
            patient()->addMedia($analysis)->toMediaCollection('analysis');
        }
        return Api::isOk('Patient information')->setData(new PatientDataResource(patient()));

    }


}
