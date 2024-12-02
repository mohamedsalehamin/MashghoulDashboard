<?php

namespace App\DefaultPanel\Requests\Api\Customer\Order;

use App\DefaultPanel\Actions\BuildCartInstanceAction;
use App\DefaultPanel\Rules\IsValidCoupon;
use App\DefaultPanel\Rules\IsValidPeriodFormatRule;
use App\DefaultPanel\Rules\IsValidReservationDateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CartCheckoutRequest extends FormRequest {
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
            'services.*.id' => ['required', Rule::exists('services', 'id')->where('provider_id', $this->route('provider')->id)->where('status', 1),],
            'coupon_code' => ['nullable', 'exists:coupons,code', new IsValidCoupon($this->cart()->getServicesTotalIncludeProducts())],
            'date' => ['required', 'date', new IsValidReservationDateRule(), new IsValidPeriodFormatRule()],
            'from' => ['required', 'date_format:H:i'],
            'to' => ['required', 'date_format:H:i'],

        ];
    }

    public function cart() {
        return BuildCartInstanceAction::run($this);
    }

}
