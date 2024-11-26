<?php

namespace App\CatalogModule\Models\Reservation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model {
    use HasFactory;
    protected $table = 'reservations_conditions';
    protected $casts = [
        'attributes' => 'array',
        'conditions' => 'array',
        'model' => 'array',
    ];
}
