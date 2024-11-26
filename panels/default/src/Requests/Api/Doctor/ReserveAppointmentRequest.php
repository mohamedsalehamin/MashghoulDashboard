<?php

namespace App\DefaultPanel\Requests\Api\Doctor;

use App\DefaultPanel\Rules\IsValidDoctorReservationDateRule;
use App\DefaultPanel\Rules\IsValidPeriodFormatRule;
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
            'date' => ['required', 'date',new IsValidDoctorReservationDateRule($this->route('doctor'))],
            'period' => ['required', 'string',new IsValidPeriodFormatRule],
            'service_id' => ['required', 'integer',Rule::exists('services','id')->where('doctor_id',$this->doctor?->user->id)],
        ];
    }

}
