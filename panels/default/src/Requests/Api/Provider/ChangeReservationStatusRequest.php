<?php

namespace App\DefaultPanel\Requests\Api\Provider;

use App\DefaultPanel\Enum\ReservationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ChangeReservationStatusRequest extends FormRequest {

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
            'status' => [
                'required',
                Rule::enum(ReservationStatus::class)
            ],
        ];
    }

}
