<?php

namespace App\Traits\Livewire;

use Illuminate\Support\Str;
use Livewire\Attributes\On;

trait HasPhoneForm {
    public $country_code = '';
    protected function prepareForValidation($attributes) {
        $attributes['phone'] = $this->fullPhone();
        return $attributes;
    }

    public function fullPhone(): array|string {
        return "+" . $this->country_code . Str::replace('+', '', $this->phone);
    }
}
