<?php

namespace App\DefaultPanel\Actions\Provider;


use App\UsersModule\Models\Provider;
use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use App\DefaultPanel\Actions\Shared\Authentication\SendVerificationCode;
use App\Exceptions\AccountNeedActivationException;
use App\Exceptions\APIException;

class ProviderHasRightsToLogin {
    use AsAction;


    /**
     * @throws Exception
     */
    public function handle() {
        $this->hasRoleManager()
            ->inBlackList()
            ->hasActiveSubscription();
    }

    /**
     * @throws Exception
     */
    public function hasRoleManager(): static {
        if (!auth()->user()->hasRole(\App\UsersModule\Models\Users\Provider::ROLE)) {
            throw new APIException(__('validation.api.invalid_credentials'), 0, null, 'invalid_credentials');
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    public function inBlackList(): static {
        if (!auth()->user()->active->value) {
            throw new APIException(__('panel.messages.your_account_not_activated'), 0, null, 'account_inactive');
        }
        return $this;
    }

    /**
     * Provider portal and mobile app require an active subscription (same as web panel middleware).
     *
     * @throws Exception
     */
    public function hasActiveSubscription(): static {
        $provider = Provider::query()->where('user_id', auth()->id())->first();

        if (! $provider) {
            throw new APIException(__('validation.api.invalid_credentials'), 0, null, 'invalid_credentials');
        }

        if (! $provider->hasActiveSubscription()) {
            throw new APIException(__('validation.api.no_active_subscription'), 0, null, 'no_active_subscription');
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    public function isPhoneVerified(): static {
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
