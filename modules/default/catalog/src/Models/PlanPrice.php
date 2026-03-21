<?php

namespace App\CatalogModule\Models;

use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPrice extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'days_count' => 'integer',
    ];

    public const PERIOD_MONTHLY = 'monthly';
    public const PERIOD_QUARTERLY = 'quarterly';
    public const PERIOD_YEARLY = 'yearly';

    public static function periods(): array
    {
        return [
            self::PERIOD_MONTHLY => 30,
            self::PERIOD_QUARTERLY => 90,
            self::PERIOD_YEARLY => 365,
        ];
    }

    public function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Money::parse($value)
        );
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getPeriodLabelAttribute(): string
    {
        return match ($this->period) {
            self::PERIOD_MONTHLY => __('panel.enums.period_monthly'),
            self::PERIOD_QUARTERLY => __('panel.enums.period_quarterly'),
            self::PERIOD_YEARLY => __('panel.enums.period_yearly'),
            default => $this->period,
        };
    }
}
