<?php

namespace App\CatalogModule\Models\Reservation;

use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\CatalogModule\Models\Reservation;
use Spatie\MediaLibrary\HasMedia;

class ItemsLine extends Model implements HasMedia {
    use HasFactory, InteractsWithMedia;
    protected $table = 'reservations_items_lines';
    protected $casts = [
        'attributes' => 'array',
        'conditions' => 'array',
        'model' => 'array',
    ];
    public function getCreatedAtColumn() {
    }

    public function reservation() {
        return $this->belongsTo(Reservation::class);
    }


}
