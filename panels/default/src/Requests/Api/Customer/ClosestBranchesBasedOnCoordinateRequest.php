<?php

namespace App\DefaultPanel\Requests\Api\Customer;


use Illuminate\Foundation\Http\FormRequest;

class ClosestBranchesBasedOnCoordinateRequest extends FormRequest {
    protected $stopOnFirstFailure = true;

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
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],

        ];
    }


}
