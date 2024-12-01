<?php

namespace App\DefaultPanel\Requests\Api\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserDeviceTokenRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    protected function prepareForValidation() {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'token' => ['required', 'string'],
        ];
    }
    public function attributes() {
        return [
            'token' => __('token'),
        ];
    }
}
