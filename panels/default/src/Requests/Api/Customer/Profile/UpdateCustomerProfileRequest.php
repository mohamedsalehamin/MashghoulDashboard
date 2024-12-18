<?php

namespace App\DefaultPanel\Requests\Api\Customer\Profile;


use App\DefaultPanel\Rules\FormatPhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerProfileRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    protected function prepareForValidation() {
        $this->merge(['name' => $this->get('full_name')]);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'first_name' => ['required', 'string', 'max:150'],
            'last_name' => ['required', 'string', 'max:150'],
            'phone' => [
                'required',
                Rule::unique('users')->ignore(auth()->id()),
                new FormatPhoneRule
            ],
            'email' => ['required', 'email', Rule::unique('users')->ignore(auth()->id())],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'dob' => ['required', 'date', 'before:' . today()->toDateString()],
            'avatar'=>['nullable', 'image']

        ];
    }
}
