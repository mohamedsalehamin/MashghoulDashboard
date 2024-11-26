<?php

namespace App\CatalogModule\Models\Reservation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model {
    protected $table = 'reservations_prescription_items';
    protected $guarded = ['id'];


    public function prescription(): BelongsTo {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }
}
