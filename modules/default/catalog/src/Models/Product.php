<?php

namespace App\CatalogModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia {

    use HasFactory, Publishable, HasTranslations, InteractsWithMedia;

    public array $translatable = [];
    protected $guarded = ['id'];
    protected $casts = [
        'title' => 'array'
    ];

    public function price(): Attribute {

        return Attribute::make(
            get: fn($value) => Money::parse($value)
        );
    }

    public function priceIncludeTaxes(): Attribute {

        $taxes = $this->service?->provider?->city?->state?->country?->taxes ?? 0;
        $price = $this->attributes['price'];
        $finalPrice = $price + ($price * $taxes / 100);

        return Attribute::make(
            get: fn($value) => Money::parse($finalPrice)
        );
    }

    public function service() {
        return $this->belongsTo(Service::class);
    }


}
