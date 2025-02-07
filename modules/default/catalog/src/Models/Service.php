<?php

namespace App\CatalogModule\Models;

use App\CatalogModule\Models\Reservation\ItemsLine;
use App\DefaultPanel\Traits\Publishable;
use App\UsersModule\Models\Provider;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Service extends Model implements HasMedia {

    use HasFactory, Publishable, HasTranslations, InteractsWithMedia, SoftDeletes;
    use LogsActivity;

    public array $translatable = ['title', 'description'];
    protected $guarded = ['id'];

    protected $casts = [
        'meta_data' => 'array',
        'title' => 'array',
        'description' => 'array',

    ];

    public function price(): Attribute {

        return Attribute::make(
            get: fn($value) => Money::parse($value)
        );
    }
    public function priceIncludeTaxes(): Attribute {

        $taxes = $this->provider?->city?->state?->country?->taxes ?? 0;
        $price = $this->attributes['price'];
        $finalPrice = $price + ($price * $taxes / 100);

        return Attribute::make(
            get: fn($value) => Money::parse($finalPrice)
        );
    }
    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }

    public function provider() {
        return $this->belongsTo(Provider::class);
    }

    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->logOnly(['title', 'description', 'duration', 'status']);
        // Chain fluent methods for configuration options

    }

    public function reservations() {
        return $this->hasManyThrough(Reservation::class, ItemsLine::class, 'service_id', 'id', 'id', 'reservation_id');
    }

    public function paidReservations() {
        return $this->reservations()->paid();
    }
}
