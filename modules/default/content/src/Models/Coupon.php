<?php

namespace App\ContentModule\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\DefaultPanel\Enum\CouponTypes;
use App\DefaultPanel\Traits\Publishable;
use App\Models\CouponProduct;
use App\Models\CouponService;
use App\Models\User;
use App\UsersModule\Models\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Coupon extends Model {
    use Publishable;

    protected $guarded = ["id"];
    protected $casts = [
        'discount_type' => CouponTypes::class,
        'meta_data' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public const SCOPE_GENERAL = 'general';
    public const SCOPE_PROVIDERS = 'providers';

    public const REQUESTED_BY_ADMIN = 'admin';
    public const REQUESTED_BY_PROVIDER = 'provider';

    public const APPLY_TARGET_ALL_ITEMS = 'all_items';
    public const APPLY_TARGET_ALL_ITEMS_WITHOUT_DISCOUNT = 'all_items_without_discount';
    public const APPLY_TARGET_SERVICES_ONLY = 'services_only';
    public const APPLY_TARGET_SERVICES_WITHOUT_DISCOUNT = 'services_without_discount';
    public const APPLY_TARGET_PRODUCTS_ONLY = 'products_only';
    public const APPLY_TARGET_PRODUCTS_WITHOUT_DISCOUNT = 'products_without_discount';

    public function formattedValue() {
        return $this->discount_type == CouponTypes::PERCENTAGE ? $this->discount_value . '%' : $this->discount_value;
    }

    public function isAvailableToUse(): bool {
        return $this->status && now()->between($this->start_date, $this->end_date);
    }

    public function isUsedBy($user): bool {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    public function isUserExceedUsageTimes($user): bool {
        return $this->users()->where('user_id', $user->id)->count() >= $this->usage_per_user;
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'coupon_user', 'coupon_id', 'user_id');
    }

    public function providers(): BelongsToMany {
        return $this->belongsToMany(Provider::class, 'coupon_provider', 'coupon_id', 'provider_id');
    }

    public function applyForUser($user): void {
        $this->users()->attach($user->id);
    }

    public function services(): HasMany {
        return $this->hasMany(CouponService::class);
    }

    /**
     * Human-readable description of what the discount applies to (for display on site).
     */
    public function appliesToLabel(): string
    {
        $by = $this->requested_by ?? self::REQUESTED_BY_ADMIN;
        if ($by === self::REQUESTED_BY_ADMIN) {
            return __('site.coupon_applies.reservation_fee');
        }

        return match ($this->apply_target) {
            self::APPLY_TARGET_ALL_ITEMS => __('site.coupon_applies.all_items'),
            self::APPLY_TARGET_ALL_ITEMS_WITHOUT_DISCOUNT => __('site.coupon_applies.all_items_without_discount'),
            self::APPLY_TARGET_SERVICES_ONLY => __('site.coupon_applies.services_only'),
            self::APPLY_TARGET_SERVICES_WITHOUT_DISCOUNT => __('site.coupon_applies.services_without_discount'),
            self::APPLY_TARGET_PRODUCTS_ONLY => __('site.coupon_applies.products_only'),
            self::APPLY_TARGET_PRODUCTS_WITHOUT_DISCOUNT => __('site.coupon_applies.products_without_discount'),
            default => __('site.coupon_applies.all_items'),
        };
    }

    public function minOrderValueAmount(): ?float
    {
        $min = (float) ($this->meta_data['min_order_value'] ?? 0);

        return $min > 0 ? $min : null;
    }

    public function minOrderAmountFormatted(): ?string
    {
        $min = $this->minOrderValueAmount();

        return $min !== null ? \Cknow\Money\Money::parse($min)->formatByDecimal() : null;
    }

    public function minOrderTypeLabel(): ?string
    {
        if ($this->minOrderValueAmount() === null) {
            return null;
        }
        $type = (string) ($this->meta_data['min_order_value_type'] ?? 'cart_total');

        return $type === 'eligible_base'
            ? __('site.coupon_min_order.type_eligible_base')
            : __('site.coupon_min_order.type_cart_total');
    }

    /**
     * Message when the cart has nothing this coupon can discount (eligible base is zero).
     */
    public function messageWhenEligibleBaseIsZero(): string
    {
        if ($this->requested_by === self::REQUESTED_BY_ADMIN) {
            return __('validation.api.coupon_admin_needs_reservation_fee');
        }

        return match ($this->apply_target ?: self::APPLY_TARGET_ALL_ITEMS) {
            self::APPLY_TARGET_PRODUCTS_ONLY => __('validation.api.coupon_requires_products_in_cart'),
            self::APPLY_TARGET_PRODUCTS_WITHOUT_DISCOUNT => __('validation.api.coupon_requires_non_sale_products'),
            self::APPLY_TARGET_SERVICES_ONLY => __('validation.api.coupon_requires_services_in_cart'),
            self::APPLY_TARGET_SERVICES_WITHOUT_DISCOUNT => __('validation.api.coupon_requires_non_sale_services'),
            self::APPLY_TARGET_ALL_ITEMS_WITHOUT_DISCOUNT => __('validation.api.coupon_requires_non_sale_items'),
            default => __('validation.api.coupon_requires_services_or_products'),
        };
    }

}
