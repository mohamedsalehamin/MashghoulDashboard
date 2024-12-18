<?php

namespace App\DefaultPanel\Requests\Api\Customer\Order;

use App\DefaultPanel\Actions\BuildCartInstanceAction;
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
            "seat_id" => ['required', 'exists:seats,id'],
            'services' => ['required', 'array'],
            'services.*.id' => ['required', Rule::exists('seat_service', 'service_id')->where('seat_id', $this->get('seat_id')),],
            'coupon_code' => ['nullable', 'exists:coupons,code', new IsValidCoupon($this->cart()->getServicesTotalIncludeProducts())],
        ];
    }

    public function cart() {
        return BuildCartInstanceAction::run($this);
    }
}
