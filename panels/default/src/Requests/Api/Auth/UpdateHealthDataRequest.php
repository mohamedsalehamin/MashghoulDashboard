<?php

namespace App\DefaultPanel\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHealthDataRequest extends FormRequest {

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
            'health_data.length' => ['nullable', 'string'],
            'health_data.blood_type' => ['nullable', 'string'],
            'health_data.blood_sugar_rate' => ['nullable', 'string'],
            'health_data.blood_pressure_rate' => ['nullable', 'string'],
            'chronic_diseases_ids' => ['nullable', 'array'],
            'chronic_diseases_ids.*' => ['nullable', 'exists:chronic_diseases,id'],
            'analysis.*' => ['nullable', 'file'],
        ];

    }


}
