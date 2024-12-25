<?php

namespace App\CatalogModule\Models;

use App\DefaultPanel\Enum\PaymentMethods;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use App\Models\User;
use Cknow\Money\Casts\MoneyDecimalCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model {
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'meta_data' => 'array',
        'price' => MoneyDecimalCast::class,
        'status' => ReservationPaymentStatus::class,


    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function scopePaid($query) {
        return $query->where('status', ReservationPaymentStatus::PAID);
    }



    public function transactionable(): MorphTo {
        return $this->morphTo("transactionable", "transactionable_type", "transactionable_id");
    }

}
