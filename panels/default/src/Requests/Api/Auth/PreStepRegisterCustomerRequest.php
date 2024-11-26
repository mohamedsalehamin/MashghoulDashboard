<?php

namespace App\DefaultPanel\Requests\Api\Auth;

use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Rules\FormatPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class PreStepRegisterCustomerRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        return [
            'phone' => [
                'required',
                'unique:users',
                new FormatPhoneRule
            ],
            'password' => [
                'required',
                Password::min(8),
            ],
            'password_confirmation' => ['required', 'same:password'],

        ];
    }
}
