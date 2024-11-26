<?php

namespace App\DefaultPanel\Actions\Customer;

use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Actions\Shared\Authentication\SendVerificationCode;
use App\Exceptions\AccountNeedActivationException;
use App\Exceptions\APIException;

class CustomerHasRightsToLogin {
    use AsAction;


    /**
     * @throws Exception
     */
    public function handle() {
        $this
//            ->hasRolePatient()
            ->isActive()
            ->isPhoneVerified();
    }

    /**
     * @throws Exception
     */
    public function hasRolePatient(): CustomerHasRightsToLogin {
        if (!auth()->user()->hasRole('patient')) {
            throw new APIException(__('Cant login as patient'));
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function isActive(): CustomerHasRightsToLogin {
        if (!auth()->user()->active) {
            throw new APIException(__('validation.api.account_suspend'));
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function isPhoneVerified(): CustomerHasRightsToLogin {
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
