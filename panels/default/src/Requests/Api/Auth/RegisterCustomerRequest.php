<?php

namespace App\DefaultPanel\Requests\Api\Auth;

use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Rules\FormatPhoneRule;
use App\DefaultPanel\Rules\IsValidVerificationCodeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class RegisterCustomerRequest extends FormRequest {

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
        if ($this->has('pre-step')) {
            return [
                'phone' => [
                    'required',
                    'unique:users',
                    new FormatPhoneRule
                ],
            ];
        }
        return [
            'first_name' => ['required', 'string', 'min:3', 'max:40'],
            'last_name' => ['required', 'string', 'min:3', 'max:40'],
            'email' => ['required', 'email', 'unique:users'],

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
            'gender' => ['required', new Enum(GenderEnum::class)],
            'dob' => ['required', 'date'],
            'state_id' => ['required', Rule::exists('states', 'id')],
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'code'=> ['required', 'string', new IsValidVerificationCodeRule()],
            'avatar'=>['nullable','image']
        ];
    }
}
