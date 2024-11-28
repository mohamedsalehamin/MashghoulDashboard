<?php

namespace App\DefaultPanel\Api\V1\Profile;


use Api;
use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Actions\SendReminderCallAction;
use App\DefaultPanel\Api\V1\Customer\Profile\Order;
use App\DefaultPanel\Enum\LabReservationStatus;
use App\DefaultPanel\Enum\OrderStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Enum\ScheduleStatusEnum;
use App\DefaultPanel\Lib\Firebase;
use App\DefaultPanel\Lib\Utils;
use App\DefaultPanel\Lib\VoipNotification;
use App\DefaultPanel\Notifications\Order\ProblematicOrderNotification;
use App\DefaultPanel\Requests\Api\Order\CancelReservationRequest;
use App\DefaultPanel\Requests\Api\Order\OrderRateRequest;
use App\DefaultPanel\Requests\Api\Order\ReportOrderRequest;
use App\DefaultPanel\Requests\Api\Order\ReportReservationRequest;
use App\DefaultPanel\Requests\Api\Order\ReservationRateRequest;
use App\DefaultPanel\Requests\Api\Order\RevisitReservationRequest;
use App\DefaultPanel\Requests\Api\Order\ScheduleReservationRequest;
use App\DefaultPanel\Resources\Api\Orders\LightOrderResource;
use App\DefaultPanel\Resources\Api\Orders\OrdersResource;
use App\DefaultPanel\Resources\Api\ReservationResource;
use App\Notifications\PatientAcceptScheduleReservationNotification;
use App\Notifications\PatientRejectScheduleReservationNotification;
use App\UsersModule\Models\Analysis;
use App\UsersModule\Models\Doctor;
use Notification;
use Tasawk\Agora\AgoraFactory;
use Tasawk\Api\Core;
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
