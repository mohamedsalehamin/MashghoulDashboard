<?php

namespace App\CatalogModule\Models;

use App\DefaultPanel\Traits\Publishable;
use App\UsersModule\Models\Provider;
use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Seat extends Model {

    use HasFactory, Publishable, HasTranslations;

    public array $translatable = ['title'];
    protected $guarded = ['id'];
    protected $casts = [
        'meta_data' => 'array'
    ];

    public function services(): BelongsToMany {
        return $this->belongsToMany(Service::class);
    }
    public function provider(): BelongsTo {
        return $this->belongsTo(Provider::class);
    }
}
