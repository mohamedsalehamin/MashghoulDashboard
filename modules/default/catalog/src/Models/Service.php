<?php

namespace App\CatalogModule\Models;

use App\DefaultPanel\Traits\Publishable;
use App\UsersModule\Models\Provider;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Service extends Model implements HasMedia {

    use HasFactory, Publishable, HasTranslations, InteractsWithMedia,SoftDeletes;

    public array $translatable = ['title', 'description'];
    protected $guarded = ['id'];

    protected $casts = [
        'meta_data' => 'array',

    ];

    public function price(): Attribute {

        return Attribute::make(
            get: fn($value) => Money::parse($value)
        );
    }

    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }

    public function provider() {
        return $this->belongsTo(Provider::class);
    }
}
