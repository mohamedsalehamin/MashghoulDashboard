<?php

namespace App\UsersModule\Models;

use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Models\Reservation\Rate;
use App\CatalogModule\Models\Seat;
use App\CatalogModule\Models\Service;
use App\CatalogModule\Models\Subscription;
use App\ContentModule\Models\Category;
use App\ContentModule\Models\City;
use App\ContentModule\Models\ProviderActivity;
use App\DefaultPanel\Enum\UserStatus;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Models\User;
use ChristianKuri\LaravelFavorite\Traits\Favoriteable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\Translatable\HasTranslations;
use Theamostafa\Wallet\Traits\HasWallet;

class Provider extends Model implements HasMedia, Sitemapable
{
    use Favoriteable;
    use HasSpatial;
    use HasTranslations, HasWallet, InteractsWithMedia;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected array $translatable = ['name', 'bio', 'meta_description', 'meta_keywords'];

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
                'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/aac',
            ]);
    }

    /**
     * Override bootHasWallet to prevent wallet creation during pluck operations
     * when the model doesn't have an ID yet.
     */
    protected static function bootHasWallet()
    {
        static::retrieved(function ($model) {
            // Only create wallet if model has an ID and wallet doesn't exist
            if ($model->id && ! $model->wallet()->exists()) {
                $model->wallet()->create(['balance' => 0, 'name' => 'Default Wallet']);
            }
        });
    }

    /** Upper bound for `meta_data.days_list` array slots checked in SQL (matches typical week + buffer). */
    private const DAYS_LIST_SLOT_SQL_CHECK_LIMIT = 24;

    public function scopeEnabled(Builder $builder): Builder
    {
        return $builder
            ->whereHas('user', fn ($q) => $q->where('active', UserStatus::ACTIVE->value))
            ->whereHas('activeSubscription')
            ->whereHas('media', fn ($q) => $q->whereIn('collection_name', ['default', 'image', 'images']))
            ->whereHas('seats')
            ->whereHas('services')
            ->whereNotNull('location')
            ->tap(fn (Builder $b) => static::applyConfiguredWorkHoursQueryConstraint($b));
    }

    /**
     * At least one working day is enabled with non-empty from/to (same rules as provider panel setup notice).
     */
    public function hasConfiguredWorkHours(): bool
    {
        $days = collect($this->meta_data['days_list'] ?? []);

        return $days->filter(function ($day) {
            if (! is_array($day)) {
                return false;
            }

            $status = $day['status'] ?? false;

            if (is_string($status)) {
                $status = $status === '1' || strtolower($status) === 'true';
            }

            $on = $status === true || $status === 1;

            $from = $day['from'] ?? null;
            $to = $day['to'] ?? null;

            return $on && $from !== null && $from !== '' && $to !== null && $to !== '';
        })->isNotEmpty();
    }

    /**
     * Restrict the query to providers with at least one enabled days_list slot that has from/to set.
     * Built in PHP (no JSON_TABLE): OR of per-index JSON_EXTRACT conditions, aligned with {@see hasConfiguredWorkHours()}.
     */
    protected static function applyConfiguredWorkHoursQueryConstraint(Builder $builder): void
    {
        $driver = $builder->getConnection()->getDriverName();
        $meta = static::qualifiedMetaDataColumn($builder);

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $parts = [];
            for ($i = 0; $i < self::DAYS_LIST_SLOT_SQL_CHECK_LIMIT; $i++) {
                $parts[] = '('.static::mysqlMariaDaysListSlotConfiguredSql($meta, $i).')';
            }
            $builder->whereRaw('('.implode(' OR ', $parts).')');

            return;
        }

        if ($driver === 'sqlite') {
            $parts = [];
            for ($i = 0; $i < self::DAYS_LIST_SLOT_SQL_CHECK_LIMIT; $i++) {
                $parts[] = '('.static::sqliteDaysListSlotConfiguredSql($meta, $i).')';
            }
            $builder->whereRaw('('.implode(' OR ', $parts).')');
        }
    }

    protected static function qualifiedMetaDataColumn(Builder $builder): string
    {
        $grammar = $builder->getConnection()->getQueryGrammar();

        return $grammar->wrapTable($builder->getModel()->getTable())
            .'.'
            .$grammar->wrap('meta_data');
    }

    /**
     * One days_list[i] row matches enabled + from + to (MySQL / MariaDB JSON functions).
     */
    protected static function mysqlMariaDaysListSlotConfiguredSql(string $meta, int $index): string
    {
        $pStatus = sprintf('$.days_list[%d].status', $index);
        $pFrom = sprintf('$.days_list[%d]."from"', $index);
        $pTo = sprintf('$.days_list[%d]."to"', $index);

        return '(('
            .'LOWER(TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT('.$meta.', \''.$pStatus.'\')), \'\'))) IN (\'true\', \'1\') '
            .'OR JSON_EXTRACT('.$meta.', \''.$pStatus.'\') = CAST(\'true\' AS JSON) '
            .'OR JSON_EXTRACT('.$meta.', \''.$pStatus.'\') = CAST(1 AS JSON)'
            .') AND CHAR_LENGTH(TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT('.$meta.', \''.$pFrom.'\')), \'\'))) > 0 '
            .'AND CHAR_LENGTH(TRIM(COALESCE(JSON_UNQUOTE(JSON_EXTRACT('.$meta.', \''.$pTo.'\')), \'\'))) > 0)';
    }

    /**
     * Same slot rule for SQLite (tests / sqlite driver).
     */
    protected static function sqliteDaysListSlotConfiguredSql(string $meta, int $index): string
    {
        $pStatus = '$.days_list['.$index.'].status';
        $pFrom = '$.days_list['.$index.'].from';
        $pTo = '$.days_list['.$index.'].to';

        return '(('
            .'LOWER(TRIM(COALESCE(CAST(json_extract('.$meta.', \''.$pStatus.'\') AS TEXT), \'\'))) IN (\'true\', \'1\') '
            .'OR CAST(json_extract('.$meta.', \''.$pStatus.'\') AS INTEGER) = 1'
            .') AND length(trim(coalesce(CAST(json_extract('.$meta.', \''.$pFrom.'\') AS TEXT), \'\'))) > 0 '
            .'AND length(trim(coalesce(CAST(json_extract('.$meta.', \''.$pTo.'\') AS TEXT), \'\'))) > 0)';
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    protected function location(): Attribute
    {
        return Attribute::make(
            set: function ($coordinate) {
                return (new Point($coordinate['lat'], $coordinate['lng']))->toSqlExpression($this->getConnection());
            }
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Primary provider image URL; falls back to the linked user's avatar when none is set on the provider.
     */
    public function getDisplayImageUrl(): string
    {
        foreach (['default', 'image'] as $collection) {
            $url = $this->getFirstMediaUrl($collection);
            if ($url !== '') {
                return $url;
            }
        }

        $user = $this->relationLoaded('user') ? $this->user : $this->user()->first();
        if ($user === null) {
            return '';
        }

        $avatar = $user->getFirstMediaUrl('avatar');

        return $avatar !== '' ? $avatar : $user->getFirstMediaUrl();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'user_id', 'user_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class, 'user_id', 'user_id')
            ->where('status', \App\DefaultPanel\Enum\SubscriptionsStatusEnum::PROCESSING)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->latest();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }

    public function reservations(): MorphMany
    {
        return $this->morphMany(Reservation::class, 'reservable');
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function rate()
    {
        return $this->hasManyThrough(Rate::class, Reservation::class, 'reservable_id', 'reservation_id');
    }

    /**
     * Get all ratings for this provider (both reservation-based and manual)
     * This includes ratings through reservations and direct manual ratings
     * Grouped by pair_id or reservation_id (one rating per group, preferring service type)
     */
    public function allRates()
    {
        // Get all matching ratings with minimal columns for grouping
        $allRatings = Rate::where(function ($query) {
            // Reservation-based ratings
            $query->whereHas('reservation', function ($q) {
                $q->where('reservable_type', static::class)
                    ->where('reservable_id', $this->id);
            })
            // OR manual ratings with this provider
                ->orWhere(function ($q) {
                    $q->where('provider_id', $this->user_id)
                        ->where('source', 'manual');
                });
        })
            ->whereNull('parent_id') // Only top-level ratings, not replies
            ->where('is_approved', true)
            ->select('id', 'type', 'pair_id', 'reservation_id', 'created_at')
            ->get();

        // Group by pair_id or reservation_id
        $grouped = $allRatings->groupBy(function ($rate) {
            return $rate->pair_id ?? $rate->reservation_id ?? 'single_'.$rate->id;
        });

        // For each group, prefer service rating, otherwise take place rating
        $selectedIds = $grouped->map(function ($group) {
            $serviceRating = $group->firstWhere('type', 'service');

            return ($serviceRating ?: $group->first())->id;
        })->values()->toArray();

        if (empty($selectedIds)) {
            return Rate::whereRaw('1 = 0'); // Return empty query
        }

        return Rate::whereIn('id', $selectedIds)->orderBy('created_at', 'desc');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function providerActivity(): BelongsTo
    {
        return $this->belongsTo(ProviderActivity::class);
    }

    public function avgRate(): float|int
    {
        return (float) $this->rate()->avg('rate') ?? 0;
    }

    /**
     * Average of approved top-level ratings (reservations + manual), same rule as API customer ProviderResource::rate.
     */
    public function getCustomerAverageRating(): float
    {
        $avg = Rate::query()
            ->where(function ($query) {
                $query->whereHas('reservation', function ($q) {
                    $q->where('reservable_type', static::class)
                        ->where('reservable_id', $this->id);
                })
                    ->orWhere(function ($q) {
                        $q->where('provider_id', $this->user_id)
                            ->where('source', Rate::SOURCE_MANUAL);
                    });
            })
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->whereNotNull('rate')
            ->avg('rate');

        return (float) ($avg ?? 0);
    }

    public function getReservationFeesIncludeTaxesAttribute(): float|int|string
    {
        $taxes = $this->city->state->country->taxes;
        $reservationFees = (new GeneralSettings)->reservations_fess;
        $taxes = $reservationFees / 100 * $taxes;

        return $taxes + $reservationFees;
    }

    public function toSitemapTag(): Url|string|array
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $locales = array_keys(config('laravellocalization.supportedLocales', ['ar' => [], 'en' => []]));
        $urls = [];
        foreach ($locales as $locale) {
            $urls[] = Url::create("{$baseUrl}/{$locale}/providers/{$this->id}")
                ->setLastModificationDate($this->updated_at ? \Carbon\Carbon::parse($this->updated_at) : now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.7);
        }

        return $urls;
    }
}
