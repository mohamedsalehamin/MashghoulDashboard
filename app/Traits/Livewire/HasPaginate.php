<?php

namespace App\Traits\Livewire;

use Livewire\WithPagination;

trait HasPaginate {
    use WithPagination;
    public $perPage = 12;
    public function more() {
        return $this->perPage = $this->perPage + 10;
    }
}
