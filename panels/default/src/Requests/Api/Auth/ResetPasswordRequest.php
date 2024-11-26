<?php

namespace App\DefaultPanel\Requests\Api\Auth;

use App\DefaultPanel\Rules\FormatPhoneRule;
use App\DefaultPanel\Rules\IsValidVerificationCodeRule;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class ResetPasswordRequest extends FormRequest {

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
            'phone' => ['required', new FormatPhoneRule],
            'code' => ['required', 'numeric', 'digits:4', new IsValidVerificationCodeRule()],
            'password' => ['required', 'confirmed',
                Password::min(8)
            ],
        ];
    }

    public function currentUser() {
        return User::where('phone', $this->get("phone"))->firstOrFail();
    }
}
