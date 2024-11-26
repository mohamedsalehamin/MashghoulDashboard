<?php

namespace App\CatalogModule\Models\Reservation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model {
    use HasFactory;

    protected $table = 'reservations_timeline';
    protected $guarded = ['id'];
    protected $casts = [
        'title' => 'json',
    ];


}
