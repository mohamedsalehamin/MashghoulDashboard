<?php

namespace App\DefaultPanel\Requests\Api\Profile;

use Illuminate\Foundation\Http\FormRequest;

class VerifyAltPhoneRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }


    public function rules() {
        return [
            'phone' => ['required', 'exists:verification_codes'],
            'code' => ['required', 'numeric', 'digits:4'],
        ];
    }

}
