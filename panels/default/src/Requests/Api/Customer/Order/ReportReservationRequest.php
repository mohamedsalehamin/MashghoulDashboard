<?php

namespace App\DefaultPanel\Requests\Api\Customer\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ReportReservationRequest extends FormRequest {

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
            "reason_id" => ['required',],
            'comment' => [Rule::requiredIf($this->reason_id == 0), 'string', 'max:512']
        ];
    }

}
