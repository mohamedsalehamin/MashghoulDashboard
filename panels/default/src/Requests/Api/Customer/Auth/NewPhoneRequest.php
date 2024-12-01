<?php

namespace App\DefaultPanel\Requests\Api\Customer\Auth;

use App\DefaultPanel\Rules\FormatPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class NewPhoneRequest extends FormRequest {

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
            'phone' => ['required',new FormatPhoneRule],
            'code' => ['required','numeric','digits:4'],
            'old_phone' => 'required',
        ];
    }
}
