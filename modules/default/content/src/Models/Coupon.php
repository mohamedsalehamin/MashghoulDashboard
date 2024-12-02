<?php

namespace App\ContentModule\Models;

use App\DefaultPanel\Enum\CouponTypes;
use App\DefaultPanel\Traits\Publishable;
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
    ];

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
}
