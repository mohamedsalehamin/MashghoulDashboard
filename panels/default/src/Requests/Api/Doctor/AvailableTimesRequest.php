<?php

namespace App\DefaultPanel\Requests\Api\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class AvailableTimesRequest extends FormRequest {

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
            'date' => ['required', 'date', 'after_or_equal:today'],

        ];
    }

}
