<?php

namespace App\DefaultPanel\Api\V1\Profile;

use Api;
use App\CatalogModule\Filters\ReservationFilter;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Resources\Api\Labs\LightReservationResource;
use App\DefaultPanel\Resources\Api\Labs\ReservationPrescriptionResource;
use App\DefaultPanel\Resources\Api\Labs\ReservationResource;
use App\UsersModule\Filters\LabFilter;
use Tasawk\Api\Core;

class MedicalTestsServices {


    public function index(): Core {
        $reservations = patient()
            ->medicalTests()
            ->paid()
            ->filter(request(), ReservationFilter::class)
            ->latest()
            ->paginate();

        return Api::isOk(__("Reservations list"), LightReservationResource::collection($reservations));
    }

    public function show(Reservation $reservation): Core {
        return Api::isOk(__("Reservation details"))->setData(ReservationResource::make($reservation));
    }




}
