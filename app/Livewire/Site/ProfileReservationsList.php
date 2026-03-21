<?php

namespace App\Livewire\Site;

use App\CatalogModule\Models\Reservation;
use Livewire\Component;
use Livewire\WithPagination;

class ProfileReservationsList extends Component
{
    use WithPagination;

    public ?string $status = null;

    public string $sort = 'newest';

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = site()->user()->reservations()
            ->with(['reservable.city']);

        if ($this->status !== null && $this->status !== '') {
            $query->where('status', $this->status);
        }

        $query->when($this->sort === 'oldest', fn ($q) => $q->orderBy('date', 'asc')->orderBy('from', 'asc'))
            ->when($this->sort !== 'oldest', fn ($q) => $q->orderBy('date', 'desc')->orderBy('from', 'desc'));

        $reservations = $query->paginate(15, ['*'], 'reservations_page');
        $reservations->withPath(parse_url(route('site.bookings'), PHP_URL_PATH));

        return view('livewire.site.profile-reservations-list', [
            'reservations' => $reservations,
        ]);
    }
}
