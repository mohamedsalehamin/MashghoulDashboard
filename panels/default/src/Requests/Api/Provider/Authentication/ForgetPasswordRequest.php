<?php

namespace App\DefaultPanel\Requests\Api\Provider\Authentication;

use App\DefaultPanel\Rules\FormatPhoneRule;
use App\DefaultPanel\Rules\ProviderPhoneExistRule;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;


class ForgetPasswordRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    protected function prepareForValidation() {

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'phone' => ['required', new FormatPhoneRule,new ProviderPhoneExistRule()],
        ];
    }

    public function currentUser() {
        return User::where('phone', $this->get("phone"))->firstOrFail();
    }


}
