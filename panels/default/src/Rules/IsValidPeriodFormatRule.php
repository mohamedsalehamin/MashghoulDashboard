<?php

namespace App\DefaultPanel\Rules;

use App\CatalogModule\Models\Service;
use App\UsersModule\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class IsValidPeriodFormatRule implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */


    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $seat = request()->route('provider')
            ->seats()
            ->where('id', request()->get('seat_id'))
            ->first();

        $interval=Service::findMany(request()->collect('services')->pluck('id'))->sum('duration');

        return collect($seat->getAvailablePeriodsOnDate(request()->date('date'),true,$interval))
            ->flatten(1)
            ->where('from', request()->get('from'))
            ->where('to', request()->get('to'))
            ->where('reserved', false)
            ->count();



    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string {
        return __("validation.api.invalid_period");
    }
}
