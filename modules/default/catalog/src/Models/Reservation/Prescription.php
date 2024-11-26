<?php

namespace App\CatalogModule\Models\Reservation;

use App\CatalogModule\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model {
    use HasFactory;
    protected $table = 'reservations_prescription';
    protected $guarded = ['id'];

    public function reservation() {
        return $this->belongsTo(Reservation::class);
    }
    public function items() {
        return $this->hasMany(PrescriptionItem::class, 'reservations_prescription_id');
    }

    public function medicines() {
     return $this->items()->where('type', 'medicine');
    }

    public function rays() {
        return $this->items()->where('type', 'ray');
    }

    public function tests() {
        return $this->items()->where('type', 'test');
    }
}
