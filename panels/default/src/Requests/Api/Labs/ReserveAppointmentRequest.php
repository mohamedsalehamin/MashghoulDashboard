<?php

namespace App\DefaultPanel\Requests\Api\Labs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReserveAppointmentRequest extends FormRequest {

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
            'date' => ['required', 'date','after_or_equal:now'],
            'time' => ['required', 'string'],
            'services' => ['required', 'array'],
            'services.*' => ['required', 'integer', Rule::exists('lab_services', 'id')->where('lab_id', $this->lab?->user?->id)],
        ];
    }

}
