<?php

namespace App\DefaultPanel\Actions\Manager;


use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Actions\Shared\Authentication\SendVerificationCode;
use App\Exceptions\AccountNeedActivationException;
use App\Exceptions\APIException;

class ManagerHasRightsToLogin {
    use AsAction;


    /**
     * @throws Exception
     */
    public function handle() {
        $this->hasRoleManager()
            ->inBlackList();
    }

    /**
     * @throws Exception
     */
    public function hasRoleManager(): ManagerHasRightsToLogin {
        if (!auth()->user()->hasRole('manager')) {
            throw new APIException(__('validation.api.invalid_credentials'));
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function inBlackList(): ManagerHasRightsToLogin {
        if (!auth()->user()->active) {
            throw new APIException(__('validation.api.account_suspend'));
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function isPhoneVerified(): ManagerHasRightsToLogin {
        if (is_null(auth()->user()->phone_verified_at)) {
            SendVerificationCode::run(auth()->user());
            throw new AccountNeedActivationException();
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    //    public function isConfirmed(): CustomerHasRightsToLogin {
    //        if (auth()->user()->admin_confirmed == 0) {
    //            throw new AccountNotApprovedException();
    //        }
    //        return $this;
    //    }

}
