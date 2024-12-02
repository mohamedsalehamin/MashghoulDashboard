<?php

namespace App\DefaultPanel\Rules;

use App\ContentModule\Models\Coupon;
use App\DefaultPanel\Enum\CouponTypes;
use Illuminate\Contracts\Validation\Rule;

class IsValidCoupon implements Rule {
    protected string $message = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(public $total) {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $valueCheckCoupon
     * @return bool
     */
    public function passes($attribute, $value) {
        $cp = Coupon::where('code', $value)
            ->first();

        $auth_user = request()->user('sanctum');

        if (!$cp) {
            $this->message = (__("validation.api.coupon_code_not_found"));
            return false;
        }
        if ($cp->providers()->count() && !$cp->providers()->pluck("provider_id")->contains(request()->route('provider')?->id)) {
            $this->message = (__("validation.api.coupon_code_not_found"));
            return false;
        }

        if ($cp?->isUserExceedUsageTimes($auth_user)) {
            $this->message = (__("validation.api.coupon_code_exceeds_the_number_of_usages_times"));
            return false;
        }
        if (!$cp->isAvailableToUse()) {
            $this->message = (__("validation.api.coupon_code_is_expired"));
            return false;
        }

        if (($cp->meta_data['min_order_value'] ?? 0) > 0 && $cp->meta_data['min_order_value'] > $this->total) {
            $this->message = (__("validation.api.coupon_code_min_order_value", ['value' => $cp->meta_data['min_order_value']]));
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return $this->message;
    }
}
