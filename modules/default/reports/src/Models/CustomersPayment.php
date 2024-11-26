<?php

namespace App\ReportsModule\Models;

use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Models\Transaction;

class CustomersPayment extends Transaction {
    protected $table='transactions';
}
