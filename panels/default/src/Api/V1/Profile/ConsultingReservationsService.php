<?php

namespace App\DefaultPanel\Api\V1\Profile;

use Api;
use App\CatalogModule\Filters\ReservationFilter;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Resources\Api\Doctors\LightReservationResource;
use App\DefaultPanel\Resources\Api\Doctors\ReservationResource;
use App\DefaultPanel\Resources\Api\Labs\ReservationPrescriptionResource;
use App\UsersModule\Filters\DoctorsFilter;
use App\UsersModule\Filters\LabFilter;
use Tasawk\Api\Core;

class ConsultingReservationsService {


    public function index(): Core {
        $reservations = patient()
            ->consultations()
            ->paid()
            ->filter(request(), ReservationFilter::class)
            ->latest()
            ->paginate();


        return Api::isOk(__("Reservations list"), LightReservationResource::collection($reservations));
    }

    public function show(Reservation $consultation): Core {
        return Api::isOk(__("Reservation details"))->setData(ReservationResource::make($consultation));
    }
    public function prescription(Reservation $consultation) {
        if (!$consultation->prescription()->exists()) {
            return Api::isOk(__("Prescription details"))->setData(collect([]));
        }
        return Api::isOk(__("Prescription details"))->setData(ReservationPrescriptionResource::make($consultation->prescription));
    }

}
