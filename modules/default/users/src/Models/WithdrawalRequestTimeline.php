<?php

namespace App\UsersModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class WithdrawalRequestTimeline extends Model
{
    protected $fillable = [
        'withdrawal_request_id',
        'status',
        'title',
        'changed_by'
    ];

    protected $casts = [
        'title' => 'json'
    ];
   

    public function withdrawalRequest(): BelongsTo
    {
        return $this->belongsTo(WithdrawalRequest::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
} 