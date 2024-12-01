<?php

namespace App\DefaultPanel\Requests\Api\Provider\Authentication;

use App\DefaultPanel\Rules\FormatPhoneRule;
use App\DefaultPanel\Rules\IsValidVerificationCodeRule;
use App\DefaultPanel\Rules\ProviderPhoneExistRule;
use Illuminate\Foundation\Http\FormRequest;;

class CodeConfirmRequest extends FormRequest {

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
            'phone' => ['required', new ProviderPhoneExistRule(), new FormatPhoneRule()],
            'code' => ['required', 'numeric', 'digits:6', new IsValidVerificationCodeRule()],
        ];
    }


}
