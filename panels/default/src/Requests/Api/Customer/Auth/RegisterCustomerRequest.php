<?php

namespace App\DefaultPanel\Requests\Api\Customer\Auth;

use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Rules\FormatPhoneRule;
use App\DefaultPanel\Rules\IsValidVerificationCodeRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

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
        return [
            'first_name' => ['required', 'string', 'min:3', 'max:40'],
            'last_name' => ['required', 'string', 'min:3', 'max:40'],
            'email' => ['nullable', 'email', 'unique:users'],
            'dob' => ['nullable', 'date',],

            'phone' => [
                'required',
                'unique:users',
                new FormatPhoneRule
            ],
            'gender' => ['required', new Enum(GenderEnum::class)],
            'state_id' => ['required', Rule::exists('states', 'id')],
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'code'=> ['required',"digits:4", new IsValidVerificationCodeRule()],
            'avatar'=>['nullable','image']
        ];
    }
}
