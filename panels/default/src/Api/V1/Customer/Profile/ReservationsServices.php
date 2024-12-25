<?php

namespace App\DefaultPanel\Api\V1\Customer\Profile;


use Api;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Requests\Api\Customer\Order\ReservationRateRequest;
use App\DefaultPanel\Resources\Api\Customer\ReservationResource;

class ReservationsServices {


    public function index() {
        $status = is_array(request('status')) ? request('status') : [request('status')];
        return Api::isOk("rated successfully", ReservationResource::collection(
            auth()->user()->reservations()
                ->when(request()->has('status'), fn($query) => $query->whereIn('status', $status))
                ->when(request()->filled('id'), fn($query) => $query->where('id', request('id')))
                ->when(request()->filled('direction'), fn($query) => $query->orderBy('date', request('direction')))
                ->get()
        ));

    }

    public function show(Reservation $reservation) {
        return Api::isOk("rated successfully", ReservationResource::make($reservation));
    }

    public function rate(ReservationRateRequest $request, Reservation $reservation) {
        $reservation->rate()->create([
            'type' => 'place',
            ...$request->collect('place')
        ]);
        $reservation->rate()->create([
            'type' => 'service',
            ...$request->collect('service')
        ]);
        return Api::isOk("rated successfully");

    }


}
