<?php

namespace App\UsersModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;
use App\DefaultPanel\Enum\WalletWithdrawEnum;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Notifications\WithdrawalRequestStatusChangedNotification;
class WithdrawalRequest extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $with = [
        'user',
        'timeline.changedBy'
    ];

    protected $fillable = [
        'user_id',
        'amount',
        'transfer_amount',
        'bank_details',
        'status',
        'withdrawable_type',
        'withdrawable_id',
        'receipt',
        'rejection_reason'
    ];

    protected $casts = [
        'bank_details' => 'array',
        'amount' => 'decimal:2',
        'status' => WalletWithdrawEnum::class,
        'rejection_reason' => 'array'
    ];

   

    protected static function boot()
    {
        parent::boot();
        static::updating(function (WithdrawalRequest $withdrawalRequest) {

            if ($withdrawalRequest->getOriginal('status') != $withdrawalRequest->status) {

                $withdrawalRequest->user->notify(new WithdrawalRequestStatusChangedNotification($withdrawalRequest));
            }

            $withdrawalRequest->addTimeline([
                'ar' => __('panel.notifications.withdrawal_request_status_changed', ['status' => __('panel.enums.' . $withdrawalRequest->status->value, [], 'ar')], 'ar'),
                'en' => __('panel.notifications.withdrawal_request_status_changed', ['status' => __('panel.enums.' . $withdrawalRequest->status->value, [], 'en')], 'en')
            ], $withdrawalRequest->status);
        }); 
    }
  
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function withdrawable(): MorphTo
    {
        return $this->morphTo();
    }
    public function addTimeline($title, $status): void
    {
        $this->timeline()->create([
            'title' => $title,
            'status' => $status,
            'changed_by' => auth()->id()
        ]);
    }
    public function timeline()
    {
        return $this->hasMany(WithdrawalRequestTimeline::class);
    }

    public function recordStatusChange($newStatus, $notes = null)
    {
        return $this->timeline()->create([
            'status' => $newStatus,
            'notes' => $notes,
            'changed_by' => auth()->id()
        ]);
    }
} 