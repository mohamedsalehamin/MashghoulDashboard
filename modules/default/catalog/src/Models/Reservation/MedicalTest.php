<?php

namespace App\CatalogModule\Models\Reservation;

use App\CatalogModule\Models\Reservation;

class MedicalTest extends Reservation {
    public function getMorphClass() {
        return Reservation::class;
    }
}
