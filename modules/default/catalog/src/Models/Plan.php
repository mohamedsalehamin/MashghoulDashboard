<?php

namespace App\CatalogModule\Models;

use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\SubscriptionsStatusEnum;
use Illuminate\Support\Facades\Route;
use App\DefaultPanel\Traits\Publishable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Plan extends Model
{
    use HasFactory, Publishable, HasTranslations;

    public array $translatable = ['name'];

    protected $guarded = ['id'];

    protected $casts = [
        'meta_data' => 'array',
        'features' => 'array',
        'is_free' => 'boolean',
        'commission_percent' => 'decimal:2',
    ];

    /**
     * Safely read one locale from a translatable attribute (avoids array values breaking Filament / Blade).
     */
    public function translationString(string $attribute, string $locale): string
    {
        $v = $this->getTranslation($attribute, $locale);
        if (is_array($v)) {
            $v = $v[$locale] ?? $v['ar'] ?? $v['en'] ?? null;
            if ($v === null) {
                foreach ($this->getTranslations($attribute) as $piece) {
                    if (is_string($piece) && $piece !== '') {
                        return $piece;
                    }
                }

                return '';
            }
        }

        return (string) ($v ?? '');
    }

    public function displayName(?string $locale = null): string
    {
        return $this->translationString('name', $locale ?? app()->getLocale());
    }

    public function planPrices(): HasMany
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)
            ->where('status', SubscriptionsStatusEnum::PROCESSING)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Expire any active subscription for the given user (provider has only one active at a time).
     */
    public static function expireActiveSubscriptionForUser(int $userId): void
    {
        Subscription::where('user_id', $userId)
            ->where('status', SubscriptionsStatusEnum::PROCESSING)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->update(['status' => SubscriptionsStatusEnum::EXIPRED]);
    }

    /**
     * Create a new subscription for the provider, copying plan features and expiring current active.
     */
    public function createSubscriptionForProvider(PlanPrice $planPrice, string $status = 'pending'): Subscription
    {
        $provider = provider();
        static::expireActiveSubscriptionForUser($provider->user_id);

        return $this->subscriptions()->create([
            'user_id' => $provider->user_id,
            'plan_price_id' => $planPrice->id,
            'status' => $status,
            'price' => $planPrice->price->getAmount(),
            'start_date' => now(),
            'end_date' => now()->addDays($planPrice->days_count),
            'features' => $this->features ?? [],
        ]);
    }

    /**
     * Subscribe the authenticated provider: payment gateway URL, or internal URL when plan is free / price is zero.
     */
    public function subscribe(PlanPrice $planPrice, string $method = 'myfatoorah'): string
    {
        $amount = (float) $planPrice->price->formatByDecimal();

        if ($this->is_free || $amount <= 0) {
            $subscription = $this->createSubscriptionForProvider($planPrice, SubscriptionsStatusEnum::PROCESSING->value);
            $subscription->transactions()->create([
                'user_id' => $subscription->user_id,
                'price' => 0,
                'status' => ReservationPaymentStatus::PAID->value,
                'meta_data' => ['method' => 'system', 'gateway' => 'system', 'paid_at' => now()->toIso8601String()],
            ]);

            return $this->freeSubscriptionSuccessUrl();
        }

        $subscription = $this->createSubscriptionForProvider($planPrice, SubscriptionsStatusEnum::PENDING->value);

        $result = $subscription->pay($planPrice->price->formatByDecimal(), $method);

        if (is_string($result)) {
            return $result;
        }
        if (is_object($result) && isset($result->meta_data['invoiceURL'])) {
            return $result->meta_data['invoiceURL'];
        }
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            $data = json_decode($result->getContent(), true);

            return $data['redirect_url'] ?? $data['invoiceURL'] ?? '';
        }

        return '';
    }

    /**
     * Where to send the provider after a free / zero-amount subscription is activated.
     */
    public function freeSubscriptionSuccessUrl(): string
    {
        if (Route::has('filament.lab-panel.resources.plans.index')) {
            return route('filament.lab-panel.resources.plans.index');
        }

        return '/';
    }
}
