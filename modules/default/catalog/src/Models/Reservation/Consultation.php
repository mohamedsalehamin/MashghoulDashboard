<?php

namespace App\CatalogModule\Models\Reservation;

use App\CatalogModule\Models\Reservation;

class Consultation extends Reservation {
    public function getMorphClass() {
        return Reservation::class;
    }
}
