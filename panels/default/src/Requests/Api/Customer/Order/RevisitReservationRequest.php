<?php

namespace App\DefaultPanel\Requests\Api\Customer\Order;

use App\DefaultPanel\Rules\IsValidDoctorReservationDateRule;
use App\DefaultPanel\Rules\IsValidPeriodFormatRule;
use Illuminate\Foundation\Http\FormRequest;


class RevisitReservationRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        return [
            'date' => ['required', 'date',new IsValidDoctorReservationDateRule($this->route('reservation')->reservable)],
            'period' => ['required', 'string',new IsValidPeriodFormatRule()],
        ];
    }

}
