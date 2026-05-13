<?php

namespace App\ReportsModule\Models;

use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Models\Transaction;

class CustomersPayment extends Transaction
{
    protected $table = 'transactions';

    protected static function booted(): void
    {
        static::addGlobalScope('reservation_customer_payments', fn ($query) => $query->where('transactionable_type', Reservation::class));
    }
}
