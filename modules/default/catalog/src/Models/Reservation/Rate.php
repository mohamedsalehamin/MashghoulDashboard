<?php

namespace App\CatalogModule\Models\Reservation;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model {
    protected $table = 'reservation_rates';
    protected $fillable = ['comment', 'rate'];
    protected $casts = [
        'rate' => 'array'
    ];
}
