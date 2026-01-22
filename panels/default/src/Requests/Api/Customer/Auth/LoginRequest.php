<?php

namespace App\DefaultPanel\Requests\Api\Customer\Auth;

use App\DefaultPanel\Rules\FormatPhoneRule;
use App\DefaultPanel\Rules\IsUserNotProvider;
use App\Exceptions\APIException;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest {
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
             'phone' => ['required', new FormatPhoneRule, new IsUserNotProvider],
        ];
    }

    /**
     * @throws APIException
     */

}
