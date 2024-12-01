<?php

namespace App\DefaultPanel\Api\V1\Customer\Profile;


use Api;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Enum\OrderStatus;
use App\DefaultPanel\Requests\Api\Customer\Order\ReservationRateRequest;
use App\DefaultPanel\Requests\Api\Order\OrderRateRequest;
use App\DefaultPanel\Requests\Api\Order\ReportOrderRequest;
use App\DefaultPanel\Resources\Api\Provider\ReservationResource;
use App\DefaultPanel\Resources\Api\Orders\LightOrderResource;
use App\DefaultPanel\Resources\Api\Orders\OrdersResource;
use Tasawk\Agora\AgoraFactory;
use Tasawk\Ecommerce\Notifications\ReservationCompletedNotification;
use Tasawk\Orders\Models\OnlineReservation;
use Tasawk\Orders\Models\OnlineReservationStatuses;
use Tasawk\Orders\Models\Order\Timeline;
use Tasawk\Orders\Models\OrderStatuses;

class ReservationsServices {


    public function index() {

        return Api::isOk("rated successfully", ReservationResource::collection(auth()->user()->reservations()->latest()->get()));

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
