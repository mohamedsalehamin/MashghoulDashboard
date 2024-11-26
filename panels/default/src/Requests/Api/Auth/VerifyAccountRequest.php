<?php

namespace App\DefaultPanel\Requests\Api\Auth;

use App\DefaultPanel\Rules\FormatPhoneRule;
use App\DefaultPanel\Rules\IsValidVerificationCodeRule;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class VerifyAccountRequest extends FormRequest {

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
            'phone' => ['required', 'exists:users',new FormatPhoneRule],

            'code' => ['required', 'numeric','digits:5', new IsValidVerificationCodeRule()],
        ];
    }

    public function currentUser() {
        return User::where('phone', $this->get("phone"))->firstOrFail();
    }
}
