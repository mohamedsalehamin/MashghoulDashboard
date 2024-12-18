<?php

namespace App\DefaultPanel\Requests\Api\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    public function rules() {
        return [
            "user_id" => ['required_without:name', 'exists:users,id'],
            "name" => ["required_without:user_id"],
            "email" => ["required_without:user_id", "email"],
            "phone" => ["required_without:user_id"],
            "title" => ["required", 'min:3'],
            "message" => ["required", "min:25"],
            'contact_type_id' => ['required'],
            'subject' => [],
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            "subject" => '',
        ]);
    }

}
