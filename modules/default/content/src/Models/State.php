<?php

namespace App\ContentModule\Models;

use App\UsersModule\Models\Clinic;
use App\UsersModule\Models\Lab;
use App\UsersModule\Models\User\Doctor;
use App\UsersModule\Models\User\Patient;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Spatie\Translatable\HasTranslations;
use App\DefaultPanel\Traits\Publishable;

class State extends Model {
    use Publishable, HasTranslations;
    use HasSpatial;

    public array $translatable = ['name'];
    protected $fillable = [
        'name',
        'country_id',
        'status',
    ];

    public function country(): BelongsTo {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany {
        return $this->hasMany(City::class);
    }
}
