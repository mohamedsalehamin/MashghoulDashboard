<?php

namespace App\Traits\Livewire;

use Livewire\Attributes\On;

trait Filterable {

    #[On('setFilter')]
    public function updateFilters($id, $value, $type = 'text'): void {
        if ($type == 'array') {

            $this->handleArrayFilters($id, $value);
            return;
        }

        $this->filters[$id] = $value;

    }

    public function handleArrayFilters($id, $value): void {
        if (isset($this->filters[$id][$value])) {
            unset($this->filters[$id][$value]);
            return;
        }
        $this->filters[$id][$value] = $value;;
    }
}
