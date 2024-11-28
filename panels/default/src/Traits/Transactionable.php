<?php

namespace App\DefaultPanel\Traits;

use App\CatalogModule\Models\Transaction;
use App\DefaultPanel\Actions\PayTransaction;
use Illuminate\Database\Eloquent\Casts\Attribute;


trait Transactionable {
    public function scopePaid($query) {
//        return $query->whereHas('transactions', fn($q) => $q->where('status', 'paid')->orWhere('status', 'refunded'));
    }


    public function transactions() {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function transaction() {
        return $this->morphOne(Transaction::class, 'transactionable')->latest();
    }


    public function pay($user = null) {
        $transaction = $this->transactions()->create(['user_id' => $this->user_id, 'price' => $this->price->formatByDecimal()]);
        return PayTransaction::run($transaction);
    }
}
