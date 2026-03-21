<?php

namespace App\Livewire\Site;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Requests\Api\Customer\Order\ReservationRateRequest;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class ReservationRateModal extends Component
{
    public Reservation $reservation;

    public int $serviceRating = 0;
    public int $placeRating = 0;
    public ?string $serviceComment = null;
    public ?string $placeComment = null;

    public ?string $error = null;

    protected $listeners = ['ratingSubmitted' => 'resetForm'];

    public function mount(Reservation $reservation): void
    {
        $this->reservation = $reservation;
    }

    public function submit(): void
    {
        $validated = Validator::make([
            'service' => ['rate' => $this->serviceRating, 'comment' => $this->serviceComment],
            'place' => ['rate' => $this->placeRating, 'comment' => $this->placeComment],
        ], [
            'service.rate' => ['required', 'numeric', 'min:1', 'max:5'],
            'service.comment' => ['nullable', 'string', 'max:512'],
            'place.rate' => ['required', 'numeric', 'min:1', 'max:5'],
            'place.comment' => ['nullable', 'string', 'max:512'],
        ])->validate();

        if (!$this->reservation->canRate()) {
            $this->error = __('validation.api.reservation_already_rated');
            return;
        }

        $pairId = \Illuminate\Support\Str::uuid()->toString();
        $providerId = $this->reservation->reservable instanceof \App\UsersModule\Models\Provider
            ? $this->reservation->reservable->user_id
            : null;

        $this->reservation->rates()->create([
            'type' => 'place',
            'provider_id' => $providerId,
            'user_id' => auth()->id(),
            'pair_id' => $pairId,
            'source' => 'reservation',
            'is_approved' => true,
            'approved_at' => now(),
            'rate' => (int) $this->placeRating,
            'comment' => $this->placeComment,
        ]);

        $this->reservation->rates()->create([
            'type' => 'service',
            'provider_id' => $providerId,
            'user_id' => auth()->id(),
            'pair_id' => $pairId,
            'source' => 'reservation',
            'is_approved' => true,
            'approved_at' => now(),
            'rate' => (int) $this->serviceRating,
            'comment' => $this->serviceComment,
        ]);

        $this->dispatch('rating-submitted');
        $this->resetForm();
        session()->flash('rating_success', __('site.heading.rating_submitted'));
        $this->redirect(route('site.booking.show', $this->reservation->id));
    }

    public function resetForm(): void
    {
        $this->serviceRating = 0;
        $this->placeRating = 0;
        $this->serviceComment = null;
        $this->placeComment = null;
        $this->error = null;
    }

    public function render()
    {
        return view('livewire.site.reservation-rate-modal');
    }
}
