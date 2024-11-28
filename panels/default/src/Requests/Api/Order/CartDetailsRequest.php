<?php

namespace App\DefaultPanel\Requests\Api\Order;

use App\DefaultPanel\Rules\IsValidCoupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CartDetailsRequest extends FormRequest {
    protected $stopOnFirstFailure = true;

    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return [
            'services' => ['required', 'array'],
            'services.*.id' => ['required', Rule::exists('services','id')->where('provider_id',$this->route('provider')->id)->where('status',1), ],
            'coupon_code' => ['nullable', 'exists:coupons,code',new IsValidCoupon()],
        ];
    }

}
