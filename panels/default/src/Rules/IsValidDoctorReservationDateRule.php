<?php

namespace App\DefaultPanel\Rules;

use App\UsersModule\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class IsValidDoctorReservationDateRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(public  $doctor) {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        if (!$this->doctor instanceof Doctor) {
            return true;
        }
        return $this->doctor->isAvailablePeriod(Carbon::parse($value), request()->input('period'));

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return __("validation.api.invalid_date_period");
    }
}
