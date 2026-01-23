<?php

namespace App\UsersModule\Models;


use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Models\Reservation\Rate;
use App\CatalogModule\Models\Seat;
use App\ContentModule\Models\Category;
use App\ContentModule\Models\City;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Models\User;
use ChristianKuri\LaravelFavorite\Traits\Favoriteable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Theamostafa\Wallet\Traits\HasWallet;

class Provider extends Model implements HasMedia {
    use InteractsWithMedia, HasWallet, HasTranslations;
    use Favoriteable;
    use HasSpatial;
    use SoftDeletes;

    protected $guarded = ['id'];
    protected array $translatable = ['name', 'bio'];

    protected $casts = [
        'location' => Point::class,
        'meta_data' => 'array',
//        'name'=>'array',
//        'bio'=>'array',
    ];

    /**
     * Override bootHasWallet to prevent wallet creation during pluck operations
     * when the model doesn't have an ID yet.
     */
    protected static function bootHasWallet() {
        static::retrieved(function ($model) {
            // Only create wallet if model has an ID and wallet doesn't exist
            if ($model->id && !$model->wallet()->exists()) {
                $model->wallet()->create(['balance' => 0, 'name' => 'Default Wallet']);
            }
        });
    }

    public function scopeEnabled($builder) {
        return $builder->whereHas("user", fn($q) => $q->where('active', 1));
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    protected function location(): Attribute {
        return Attribute::make(
            set: function ($coordinate) {
                return (new Point($coordinate['lat'], $coordinate['lng']))->toSqlExpression($this->getConnection());
            }
        );
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function reservations(): MorphMany {
        return $this->morphMany(Reservation::class, 'reservable');
    }

    public function seats() {
        return $this->hasMany(Seat::class);
    }

    public function rate() {
        return $this->hasManyThrough(Rate::class, Reservation::class, 'reservable_id', 'reservation_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function avgRate(): float|int {
        return (float)$this->rate()->avg('rate') ?? 0;
    }

    public function getReservationFeesIncludeTaxesAttribute(): float|int|string {
        $taxes = $this->city->state->country->taxes;
        $reservationFees = (new GeneralSettings())->reservations_fess;
        $taxes = $reservationFees / 100 * $taxes;
        return $taxes + $reservationFees;
    }
}
