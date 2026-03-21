<?php

namespace App\ProviderPanel\Filament\Resources\PlanResource\Pages;

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\PlanPrice;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\SubscriptionsStatusEnum;
use App\ProviderPanel\Filament\Resources\PlanResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\View;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Collection;

class ListPlans extends ListRecords
{
    protected static string $resource = PlanResource::class;

    public string $selectedPeriod = 'monthly'; // monthly, quarterly, yearly
    public ?int $selectedPlanId = null;
    public ?int $selectedPlanPriceId = null;
    public string $paymentMethod = 'myfatoorah'; // myfatoorah, tabby, wallet

    public function selectPlan(int $planId, int $planPriceId): void
    {
        $this->selectedPlanId = $planId;
        $this->selectedPlanPriceId = $planPriceId;
    }

    public function subscribeToPlan(int $planId, int $planPriceId): void
    {
        $this->selectPlan($planId, $planPriceId);
        $this->subscribe();
    }

    public function subscribe(): void
    {
        if (!$this->selectedPlanId || !$this->selectedPlanPriceId) {
            Notification::make()->title(__('panel.messages.warning'))->danger()->send();
            return;
        }

        $plan = Plan::with('planPrices')->find($this->selectedPlanId);
        $planPrice = PlanPrice::find($this->selectedPlanPriceId);

        if (!$plan || !$planPrice || $planPrice->plan_id !== $plan->id) {
            Notification::make()->title(__('panel.messages.warning'))->danger()->send();
            return;
        }

        if ($this->paymentMethod === 'wallet') {
            $provider = provider();
            $amount = (float) $planPrice->price->formatByDecimal();

            if ($plan->is_free || $amount <= 0) {
                $subscription = $plan->createSubscriptionForProvider($planPrice, SubscriptionsStatusEnum::PROCESSING->value);
                $subscription->transactions()->create([
                    'user_id' => $subscription->user_id,
                    'price' => 0,
                    'status' => ReservationPaymentStatus::PAID->value,
                    'meta_data' => ['method' => 'system', 'gateway' => 'system', 'paid_at' => now()->toIso8601String()],
                ]);
                Notification::make()->title(__('panel.messages.success'))->success()->send();
                $this->selectedPlanId = null;
                $this->selectedPlanPriceId = null;

                return;
            }

            if ($provider->balance < $amount) {
                Notification::make()
                    ->title(__('panel.messages.you_dont_have_enough_money_to_pay'))
                    ->danger()
                    ->send();
                return;
            }
            $provider->withdraw($amount, [
                'description' => [
                    'ar' => __('panel.messages.pay_subscription_via_wallet', ['amount' => $amount, 'id' => $plan->id], 'ar'),
                    'en' => __('panel.messages.pay_subscription_via_wallet', ['amount' => $amount, 'id' => $plan->id], 'en'),
                ],
            ]);
            $subscription = $plan->createSubscriptionForProvider($planPrice, SubscriptionsStatusEnum::PROCESSING->value);
            $subscription->transactions()->create([
                'user_id' => $subscription->user_id,
                'price' => $planPrice->price->formatByDecimal(),
                'status' => ReservationPaymentStatus::PAID->value,
            ]);
            Notification::make()->title(__('panel.messages.success'))->success()->send();
            $this->selectedPlanId = null;
            $this->selectedPlanPriceId = null;
        } else {
            $url = $plan->subscribe($planPrice, $this->paymentMethod);
            if ($url) {
                $this->redirect($url);

                return;
            }
            Notification::make()->title(__('panel.messages.warning'))->danger()->send();
        }
    }

    public function clearSelection(): void
    {
        $this->selectedPlanId = null;
        $this->selectedPlanPriceId = null;
    }

    public function selectPeriod(string $period): void
    {
        $this->selectedPeriod = $period;
        $this->clearSelection();
    }

    /**
     * Not #[Computed]: Filament schema + custom view must re-resolve plans when
     * selectedPeriod changes so quarterly/yearly prices update in the UI.
     */
    public function plans(): Collection
    {
        return Plan::query()
            ->enabled()
            ->with(['planPrices' => fn ($q) => $q->orderBy('period')])
            ->orderByDesc('is_free')
            ->orderBy('id')
            ->get();
    }

    public function providerBalance(): float
    {
        return (float) (provider()?->balance ?? 0);
    }

    public function getPriceForPeriod($plan): ?PlanPrice
    {
        $period = (string) $this->selectedPeriod;

        $match = $plan->planPrices->first(
            fn (PlanPrice $p) => strcasecmp((string) $p->period, $period) === 0
        );

        if ($match) {
            return $match;
        }

        // Backward compatibility: single price row or legacy data
        return $plan->planPrices->first();
    }

    public function content(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                View::make('provider-panel::plans.list')->viewData(fn (): array => [
                    'livewire' => $this,
                    // Pass collections as view data — $livewire->plans / $livewire->providerBalance trigger
                    // Livewire __get('plans') and throw PropertyNotFoundException (they are methods, not properties).
                    'plans' => $this->plans(),
                    'providerBalance' => $this->providerBalance(),
                ]),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }
}
