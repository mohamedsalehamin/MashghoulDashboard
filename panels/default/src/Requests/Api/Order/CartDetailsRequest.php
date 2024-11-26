<?php

namespace App\DefaultPanel\Requests\Api\Order;

use App\DefaultPanel\Rules\IsRequiredProductOptionsRepresentRule;
use App\DefaultPanel\Rules\IsValidCoupon;
use App\DefaultPanel\Rules\IsValidProductOptionsRule;
use App\DefaultPanel\Rules\IsValidProductOptionValuesRule;
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
            'products' => ['required', 'array'],
            'products.*.id' => ['required', Rule::exists('products','id')->where('status',1), new IsRequiredProductOptionsRepresentRule()],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
            'products.*.options.*.id' => [new IsValidProductOptionsRule()],
            'products.*.options.*.value_id' => [new IsValidProductOptionValuesRule()],
            'coupon_code' => ['nullable', 'exists:coupons,code',new IsValidCoupon()],
        ];
    }

}
