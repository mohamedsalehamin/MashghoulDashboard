<?php

namespace App\Models;

use App\CatalogModule\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointsUsage extends Model {
    use HasFactory;

    protected $guarded = ['id'];

    public function reservation() {
        return $this->belongsTo(Reservation::class);
    }
}
