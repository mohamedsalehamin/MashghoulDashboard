<?php

namespace App\CatalogModule\Models;

use App\CatalogModule\Models\Reservation\ItemsLine;
use App\DefaultPanel\Traits\Publishable;
use App\UsersModule\Models\Provider;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
    public function salePrice(): Attribute {
        return Attribute::make(
            get: fn($value) =>  Money::parse($value)
        );
    }

    public function finalPrice(): Attribute {
        return Attribute::make(
            get: function() {
                if ($this->sale_price && $this->sale_price > 0) {
                    return $this->sale_price;
                }
                return $this->price;
            }
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
     public function salePriceIncludeTaxes(): Attribute
    {

        $taxes = $this->provider?->city?->state?->country?->taxes ?? 0;

        $price = $this->attributes['sale_price'];
        $finalPrice = $this->attributes['sale_price'] > 0  ? $price + ($price * $taxes / 100) : 0;

        return Attribute::make(
            get: fn($value) => Money::parse($finalPrice)
        );
    }
    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }

    public function seats(): BelongsToMany {
        return $this->belongsToMany(Seat::class, 'seat_service')
            ->withPivot('service_group_id');
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

    /**
     * Cover image URL for this service row only (media on {@see Service}, not User).
     * Filament uses Spatie collection name `avatar` for the file upload field — unrelated to account avatars.
     */
    public function getServiceImageUrl(): string
    {
        foreach (['avatar', 'default', 'image'] as $collection) {
            $url = $this->getFirstMediaUrl($collection);
            if ($url !== '') {
                return $url;
            }
        }

        return '';
    }

    public function paidReservations() {
        return $this->reservations()->paid();
    }
}
