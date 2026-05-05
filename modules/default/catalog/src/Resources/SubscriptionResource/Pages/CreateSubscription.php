<?php

namespace App\CatalogModule\Resources\SubscriptionResource\Pages;

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\PlanPrice;
use App\CatalogModule\Models\Subscription;
use App\CatalogModule\Resources\SubscriptionResource;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\SubscriptionsStatusEnum;
use App\DefaultPanel\Enum\UserStatus;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $plan = Plan::find($data['plan_id'] ?? null);
        $planPrice = PlanPrice::find($data['plan_price_id'] ?? null);

        Plan::expireActiveSubscriptionForUser((int) $data['user_id']);

        $data['status'] = SubscriptionsStatusEnum::PROCESSING->value;
        $data['features'] = $plan?->features ?? [];
        $data['price'] = $planPrice ? $planPrice->price->getAmount() : 0;
        $data['plan_snapshot'] = $plan ? Subscription::buildPlanSnapshot($plan, $planPrice) : null;

        return $data;
    }

    protected function afterCreate(): void
    {
        $subscription = $this->record;

        $subscription->transactions()->create([
            'user_id' => $subscription->user_id,
            'price' => $subscription->price->formatByDecimal(),
            'status' => ReservationPaymentStatus::PAID->value,
            'meta_data' => ['method' => 'system', 'gateway' => 'system', 'paid_at' => now()->toIso8601String()],
        ]);

        User::where('id', $subscription->user_id)->update(['active' => UserStatus::ACTIVE]);
    }
}
