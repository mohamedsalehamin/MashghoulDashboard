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
}
