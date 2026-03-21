<?php

namespace App\ReportsModule\Models;

use App\CatalogModule\Models\Subscription;
use App\CatalogModule\Models\Transaction;

class SubscriptionPayment extends Transaction
{
    protected $table = 'transactions';

    public static function booted(): void
    {
        static::addGlobalScope('subscription', fn ($query) => $query->where('transactionable_type', Subscription::class));
    }
}
