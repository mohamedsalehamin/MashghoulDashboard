<?php

namespace App\CatalogModule\Models\Reservation;

use App\CatalogModule\Models\Reservation;
use App\Models\User;
use App\UsersModule\Models\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rate extends Model
{
    protected $table = 'reservation_rates';

    protected $fillable = [
        'comment',
        'rate',
        'type',
        'reservation_id',
        'provider_id',
        'user_id',
        'parent_id',
        'pair_id',
        'source',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'rate' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Validate that rate is required for non-reply ratings
        static::creating(function ($rate) {
            // Only validate if source is not 'reply' and parent_id is null
            if ($rate->source !== self::SOURCE_REPLY && is_null($rate->parent_id)) {
                if (is_null($rate->rate)) {
                    $validator = \Illuminate\Support\Facades\Validator::make([], []);
                    $validator->errors()->add('rate', __('validation.required', ['attribute' => 'rate']));
                    throw new \Illuminate\Validation\ValidationException($validator);
                }
            }
        });

        static::updating(function ($rate) {
            // Only validate if source is not 'reply' and parent_id is null
            if ($rate->source !== self::SOURCE_REPLY && is_null($rate->parent_id)) {
                if (is_null($rate->rate)) {
                    $validator = \Illuminate\Support\Facades\Validator::make([], []);
                    $validator->errors()->add('rate', __('validation.required', ['attribute' => 'rate']));
                    throw new \Illuminate\Validation\ValidationException($validator);
                }
            }
        });
    }

    /**
     * Source types
     */
    const SOURCE_RESERVATION = 'reservation';
    const SOURCE_MANUAL = 'manual';
    const SOURCE_REPLY = 'reply';

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * The reservation this rating belongs to (nullable for manual ratings)
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * The provider being rated
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * The provider model (through users table)
     */
    public function providerProfile(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id', 'user_id');
    }

    /**
     * The user who gave the rating
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The admin who approved this rating
     */
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Parent rating (if this is a reply)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Rate::class, 'parent_id');
    }

    /**
     * Replies to this rating
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Rate::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get the paired rating (service <-> place)
     * For reservation-based: same reservation_id, different type
     * For manual: same pair_id, different type
     */
    public function getPairedRatingAttribute(): ?Rate
    {
        if ($this->pair_id) {
            return static::where('pair_id', $this->pair_id)
                ->where('id', '!=', $this->id)
                ->first();
        }

        if ($this->reservation_id) {
            return static::where('reservation_id', $this->reservation_id)
                ->where('type', '!=', $this->type)
                ->whereNull('parent_id')
                ->first();
        }

        return null;
    }

    /**
     * Get service rating from the pair
     */
    public function getServiceRatingAttribute(): ?Rate
    {
        if ($this->type === 'service') {
            return $this;
        }
        return $this->paired_rating?->type === 'service' ? $this->paired_rating : null;
    }

    /**
     * Get place rating from the pair
     */
    public function getPlaceRatingAttribute(): ?Rate
    {
        if ($this->type === 'place') {
            return $this;
        }
        return $this->paired_rating?->type === 'place' ? $this->paired_rating : null;
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Only top-level ratings (not replies)
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Only replies
     */
    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Reservation-based ratings
     */
    public function scopeReservationBased($query)
    {
        return $query->where('source', self::SOURCE_RESERVATION);
    }

    /**
     * Manual ratings (admin-created)
     */
    public function scopeManual($query)
    {
        return $query->where('source', self::SOURCE_MANUAL);
    }

    /**
     * Only approved ratings
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Only pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * For a specific provider
     */
    public function scopeForProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    // ========================================
    // HELPERS
    // ========================================

    /**
     * Check if this is a reply
     */
    public function isReply(): bool
    {
        return $this->source === self::SOURCE_REPLY || !is_null($this->parent_id);
    }

    /**
     * Check if this is a manual rating
     */
    public function isManual(): bool
    {
        return $this->source === self::SOURCE_MANUAL;
    }

    /**
     * Check if this is a reservation-based rating
     */
    public function isReservationBased(): bool
    {
        return $this->source === self::SOURCE_RESERVATION;
    }

    /**
     * Approve this rating
     */
    public function approve(?int $approvedBy = null): bool
    {
        return $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $approvedBy ?? auth()->id(),
        ]);
    }

    /**
     * Reject/unapprove this rating
     */
    public function reject(): bool
    {
        return $this->update([
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    /**
     * Create a reply to this rating
     */
    public function createReply(string $comment, int $userId): Rate
    {
        return static::create([
            'parent_id' => $this->id,
            'provider_id' => $this->provider_id,
            'reservation_id' => $this->reservation_id, // Preserve reservation_id if exists
            'pair_id' => $this->pair_id, // Preserve pair_id if exists
            'user_id' => $userId,
            'comment' => $comment,
            'rate' => null,
            'type' => 'reply',
            'source' => self::SOURCE_REPLY,
            'is_approved' => true, // Provider replies are auto-approved
        ]);
    }

    /**
     * Get the display name for the rating source
     */
    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            self::SOURCE_RESERVATION => __('Reservation Rating'),
            self::SOURCE_MANUAL => __('Manual Rating'),
            self::SOURCE_REPLY => __('Provider Reply'),
            default => $this->source,
        };
    }

    /**
     * Get service rating display (for infolist)
     */
    public function getServiceRateDisplayAttribute(): string
    {
        $serviceRating = $this->getServiceRating();
        if (!$serviceRating || !$serviceRating->rate) {
            return '-';
        }
        return str_repeat('⭐', $serviceRating->rate) . " ({$serviceRating->rate}/5)";
    }

    /**
     * Get service comment display (for infolist)
     */
    public function getServiceCommentDisplayAttribute(): string
    {
        $serviceRating = $this->getServiceRating();
        return $serviceRating?->comment ?: '-';
    }

    /**
     * Get place rating display (for infolist)
     */
    public function getPlaceRateDisplayAttribute(): string
    {
        $placeRating = $this->getPlaceRating();
        if (!$placeRating || !$placeRating->rate) {
            return '-';
        }
        return str_repeat('⭐', $placeRating->rate) . " ({$placeRating->rate}/5)";
    }

    /**
     * Get place comment display (for infolist)
     */
    public function getPlaceCommentDisplayAttribute(): string
    {
        $placeRating = $this->getPlaceRating();
        return $placeRating?->comment ?: '-';
    }

    /**
     * Get service rating (helper method)
     */
    private function getServiceRating(): ?Rate
    {
        if ($this->type === 'service') {
            return $this;
        }

        if ($this->pair_id) {
            return static::where('pair_id', $this->pair_id)
                ->where('type', 'service')
                ->whereNull('parent_id')
                ->first();
        }

        if ($this->reservation_id) {
            return static::where('reservation_id', $this->reservation_id)
                ->where('type', 'service')
                ->whereNull('parent_id')
                ->first();
        }

        return null;
    }

    /**
     * Get place rating (helper method)
     */
    private function getPlaceRating(): ?Rate
    {
        if ($this->type === 'place') {
            return $this;
        }

        if ($this->pair_id) {
            return static::where('pair_id', $this->pair_id)
                ->where('type', 'place')
                ->whereNull('parent_id')
                ->first();
        }

        if ($this->reservation_id) {
            return static::where('reservation_id', $this->reservation_id)
                ->where('type', 'place')
                ->whereNull('parent_id')
                ->first();
        }

        return null;
    }
}
