<?php

namespace App\CatalogModule\Models\Reservation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revisit extends Model {
    use HasFactory;


    protected $table = 'revisit_reservations';
    protected $guarded = ['id'];
    protected $casts = [
        'date' => 'datetime',
    ];
}
