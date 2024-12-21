<?php

namespace App\DefaultPanel\Api\V1\Provider;


use Api;
use App\ContentModule\Models\JoinRequest;
use App\DefaultPanel\Actions\Provider\ProviderHasRightsToLogin;
use App\DefaultPanel\Actions\Shared\Authentication\ForgetPassword;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserPassword;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserToken;
use App\DefaultPanel\Requests\Api\Provider\Authentication\RegisterRequest;
use App\DefaultPanel\Resources\Api\Provider\ProviderAccountResources;
use App\DefaultPanel\Resources\Api\Provider\ProviderResources;
use Auth;

use Tasawk\Api\Core;
use App\DefaultPanel\Requests\Api\Provider\Authentication\CodeConfirmRequest;
use App\DefaultPanel\Requests\Api\Provider\Authentication\ForgetPasswordRequest;
use App\DefaultPanel\Requests\Api\Provider\Authentication\LoginRequest;
use App\DefaultPanel\Requests\Api\Provider\Authentication\ResetPasswordRequest;


class AuthServices {

    public function login(LoginRequest $request) {
        if (!Auth::once($request->only("phone", 'password'))) {
            return Api::isError(__('validation.api.invalid_credentials'))->setErrors(['credentials' => __('validation.api.invalid_credentials')]);
        }
        ProviderHasRightsToLogin::run();
        UpdateUserToken::run(auth()->user());

        return Api::isOk(__("Provider information"))->setData( ProviderAccountResources::make(auth()->user()));
    }


    public function verifySMSCode(CodeConfirmRequest $request): Core {
        return Api::isOk(__("Correct Verification code"));

    }

    public function forgetPassword(ForgetPasswordRequest $request): Core {
        ForgetPassword::run($request->currentUser());
        return Api::isOk(__("SMS code sent"));

    }

    public function resetPassword(ResetPasswordRequest $request): Core {
        UpdateUserPassword::run($request->currentUser(), $request->get('password'));
        return Api::isOk(__("User information"))->setData(new ProviderAccountResources($request->currentUser()));

    }

    public function register(RegisterRequest $request) {
        JoinRequest::create($request->validated());
        return Api::isOk(__("done"));
    }

}
