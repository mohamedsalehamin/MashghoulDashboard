<?php

namespace App\DefaultPanel\Requests\Api\Order;

use Illuminate\Foundation\Http\FormRequest;


class ReservationRateRequest extends FormRequest {

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
            "rate" => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:512']
        ];
    }

}
