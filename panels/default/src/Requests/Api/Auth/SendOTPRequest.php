<?php

namespace App\DefaultPanel\Requests\Api\Auth;

use App\DefaultPanel\Rules\FormatPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

class SendOTPRequest extends FormRequest {

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
        ];
    }



}
