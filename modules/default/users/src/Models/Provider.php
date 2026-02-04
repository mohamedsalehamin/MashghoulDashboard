<?php

namespace App\UsersModule\Models;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portfolio')
            ->acceptsMimeTypes([
                // Images
                'image/jpeg', 'image/png', 'image/webp', 'image/gif',
                // Videos
                'video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'video/webm',
                // Audio
                'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/aac'
            ]);
    }

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

    /**
     * Get all ratings for this provider (both reservation-based and manual)
     * This includes ratings through reservations and direct manual ratings
     * Grouped by pair_id or reservation_id (one rating per group, preferring service type)
     */
    public function allRates() {
        // Get all matching ratings with minimal columns for grouping
        $allRatings = Rate::where(function($query) {
            // Reservation-based ratings
            $query->whereHas('reservation', function($q) {
                $q->where('reservable_type', static::class)
                  ->where('reservable_id', $this->id);
            })
            // OR manual ratings with this provider
            ->orWhere(function($q) {
                $q->where('provider_id', $this->user_id)
                  ->where('source', 'manual');
            });
        })
        ->whereNull('parent_id') // Only top-level ratings, not replies
        ->where('is_approved', true)
        ->select('id', 'type', 'pair_id', 'reservation_id', 'created_at')
        ->get();

        // Group by pair_id or reservation_id
        $grouped = $allRatings->groupBy(function($rate) {
            return $rate->pair_id ?? $rate->reservation_id ?? 'single_' . $rate->id;
        });

        // For each group, prefer service rating, otherwise take place rating
        $selectedIds = $grouped->map(function($group) {
            $serviceRating = $group->firstWhere('type', 'service');
            return ($serviceRating ?: $group->first())->id;
        })->values()->toArray();

        if (empty($selectedIds)) {
            return Rate::whereRaw('1 = 0'); // Return empty query
        }

        return Rate::whereIn('id', $selectedIds)->orderBy('created_at', 'desc');
    }

    public function category(): BelongsTo {
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
