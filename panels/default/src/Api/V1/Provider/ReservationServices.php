<?php

namespace App\DefaultPanel\Api\V1\Provider;


use Api;
use App\CatalogModule\Models\Commission;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserPassword;
use App\DefaultPanel\Requests\Api\Provider\ChangeReservationStatusRequest;
use App\DefaultPanel\Requests\Api\Provider\UpdatePasswordRequest;
use App\DefaultPanel\Resources\Api\Provider\LightReservationResource;
use App\DefaultPanel\Resources\Api\Provider\ProviderAccountResources;
use App\DefaultPanel\Resources\Api\Provider\ReservationResource;
use App\UsersModule\Models\Users\Customer;
use Cknow\Money\Money;
use DB;


class ReservationServices {

    public function index() {

        $reservations = Reservation::query()
            ->paid()
            ->where('reservable_id', provider()->id)
            ->when(request()->has('status'), fn($query) => $query->where('status', request('status')))
            ->when(request()->filled('date_from'), fn($query) => $query->whereDate('date', '>=', request('date_from')))
            ->when(request()->filled('date_to'), fn($query) => $query->whereDate('date', '<=', request('date_to')))
            ->when(request()->filled('id'), fn($query) => $query->where('id', request('id')))
            ->when(request()->filled('direction'), fn($query) => $query->orderBy('date', request('direction')))
            ->paginate(10);
        return Api::isOk(__("Reservations list"), LightReservationResource::collection($reservations));
    }

    public function show(Reservation $reservation) {
        return Api::isOk(__("Reservation info"), ReservationResource::make($reservation));
    }

    public function changeStatus(ChangeReservationStatusRequest $request, Reservation $reservation) {
        $reservation->update(['status' => $request->status]);
        return Api::isOk(__("Reservation status changed"), ReservationResource::make($reservation));
    }

    public function statistics() {
        $counts = $this->stats();
        return Api::isOk(__("Statistics"), [
            'reservations_total' =>Money::parse( provider()->reservations()->paid()->sum('price'))->format(),
            'profits' => Money::parse(provider()->balance)->format(),
            'customers' => Customer::whereHas('reservations', fn($query) => $query->where('reservable_id', provider()->id)->paid())->count(),
            'seats_count' => provider()->seats()->count(),
            'reservations_count' => provider()->reservations()->paid()->count(),
            'stats' => [
                'new' =>(int) $counts->pending,
                'in_processing' =>(int) $counts->in_processing,
                'completed' => (int)$counts->completed,
                'canceled' => (int)$counts->canceled
            ]

        ]);
    }

    public function stats() {
        return Reservation::paid()
            ->where('reservable_id', provider()?->id)
            ->select(DB::raw("count(id) AS 'all', sum( CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS 'pending', sum( CASE WHEN status in( 'processing') THEN 1 ELSE 0 END) AS 'in_processing', sum( CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS 'completed', sum( CASE WHEN status in( 'canceled') THEN 1 ELSE 0 END) AS 'canceled' "))->first();
    }

}
