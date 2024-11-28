<?php

namespace App\CatalogModule\Models\Reservation;

use App\CatalogModule\Models\Reservation;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model {
    protected $table = 'reservation_rates';
    protected $fillable = ['comment', 'rate','type'];
    protected $casts = [
        'rate' => 'array'
    ];

    public function reservation() {
        return $this->belongsTo(Reservation::class);
    }
}
