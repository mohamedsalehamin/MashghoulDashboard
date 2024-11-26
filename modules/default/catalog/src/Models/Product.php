<?php

namespace App\CatalogModule\Models;

use App\DefaultPanel\Traits\Publishable;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Product extends Model {

    use HasFactory, Publishable, HasTranslations;

    public array $translatable = ['name'];
    protected $guarded = ['id'];

    public function price(): Attribute {

        return Attribute::make(
            get: fn($value) => Money::parse($value)
        );
    }


}
