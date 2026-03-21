<?php

namespace App\Livewire\Site;

use App\ContentModule\Models\Level;
use App\Models\PointsExchange;
use Livewire\Component;

class RedeemModal extends Component
{
    public $selectedPlanId = null;

    public function mount()
    {
        $plans = Level::enabled()->latest()->get();
        $user = site()->user();
        $totalPoints = $user->getTotalPoints();

        $canExchange = $plans->filter(fn (Level $p) => $p->canExchangeByUser($user));
        $firstAvailable = $canExchange->sortBy('value')->first();
        $this->selectedPlanId = $firstAvailable?->id;
    }

    public function confirmExchange()
    {
        $plan = Level::find($this->selectedPlanId);
        if (! $plan) {
            $this->addError('selectedPlanId', __('site.messages.insufficient_points'));
            return;
        }

        $user = site()->user();
        if (! $plan->canExchangeByUser($user)) {
            $this->addError('selectedPlanId', __('site.messages.insufficient_points'));
            return;
        }

        $points = $user->points()->where('transferred', false);
        $userTotalPoints = $points->sum('reset_points');
        $planTotalPoints = $plan->value;

        if ($userTotalPoints < $planTotalPoints) {
            $this->addError('selectedPlanId', __('site.messages.insufficient_points'));
            return;
        }

        try {
            foreach ($points->get() as $point) {
                if ($planTotalPoints >= ($point->reset_points ?? 0)) {
                    $planTotalPoints -= $point->reset_points ?? 0;
                    $point->update(['transferred' => true, 'reset_points' => 0]);
                } else {
                    $point->update(['reset_points' => ($point->reset_points ?? 0) - $planTotalPoints]);
                    break;
                }
            }

            PointsExchange::create([
                'user_id' => $user->id,
                'level_id' => $plan->id,
                'points' => $plan->value,
                'price' => $plan->price,
                'reset_price' => $plan->price,
                'used' => false,
                'expired_at' => now()->addDays((int) $plan->duration),
            ]);

            $this->selectedPlanId = null;
            $this->dispatch('points-redeemed');
            $this->dispatch('refresh-rewards-list');
        } catch (\Throwable $e) {
            $this->addError('selectedPlanId', __('site.messages.exchange_failed'));
        }
    }

    public function closeModal()
    {
        $this->selectedPlanId = null;
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function render()
    {
        $plans = Level::enabled()->latest()->get();
        $totalPoints = site()->user()->getTotalPoints();
        $canExchangePlans = $plans->filter(fn (Level $p) => $p->canExchangeByUser(site()->user()));

        return view('livewire.site.redeem-modal', [
            'plans' => $plans,
            'totalPoints' => $totalPoints,
            'canExchangePlans' => $canExchangePlans,
            'errors' => $this->getErrorBag()->getMessages(),
        ]);
    }
}
