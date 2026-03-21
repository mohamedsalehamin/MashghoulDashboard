<?php

namespace App\Livewire\Site;

use Livewire\Component;
use Livewire\WithPagination;

class ProfileBookingsList extends Component
{
    use WithPagination;

    public ?string $status = null;

    public function render()
    {
        $locale = app()->getLocale();
        $reservations = site()->user()->reservations()
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderBy('date', 'desc')
            ->orderBy('from', 'desc')
            ->paginate(15);

        return view('livewire.site.profile-bookings-list', [
            'reservations' => $reservations,
            'locale' => $locale,
        ]);
    }
}
