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


    public function rate(ReservationRateRequest $request, Reservation $reservation) {
        $reservation->rate()->create($request->validated());
        return Api::isOk("rated successfully");

    }


    public function report(ReportReservationRequest $request, Reservation $reservation): Core {
        $reservation->report()->create($request->validated());
        $reservation->update(['status' => ReservationStatus::PROBLEMATIC]);
        return Api::isOk("reported");
    }

    public function cancel(CancelReservationRequest $request, Reservation $reservation): Core {
        $reservation->cancellation()->create($request->validated());
        $reservation->update(['status' => ReservationStatus::PATIENT_CANCELED]);
        return Api::isOk("canceled");
    }

    public function schedule(ScheduleReservationRequest $request, Reservation $reservation): Core {
        $reservation->scheduleAppointment($request->get('date'), $request->get('period'), 'patient');
        return Api::isOk("scheduled");

    }

    public function revisit(RevisitReservationRequest $request, Reservation $reservation): Core {
        if (!is_a($reservation->reservable, Doctor::class)) {
            return Api::isError("only doctor reservations can be revisited");
        }
        $reservation->revisitAppointment(...$request->validated());
        return Api::isOk("revisited");
    }

    public function confirm(Reservation $reservation): Core {
        $reservation->update(['status' => LabReservationStatus::PROCESSING]);
        return Api::isOk("confirmed");
    }

    public function rejectScheduleReservationDate(Reservation $reservation) {
        $reservation->schedule()->update(['status' => ScheduleStatusEnum::REJECTED]);
        $reservation->reservable->user->notify(new PatientRejectScheduleReservationNotification($reservation));
        return Api::isOk('done');
    }

    public function acceptScheduleReservationDate(Reservation $reservation) {
        $reservation->schedule()->update(['status' => ScheduleStatusEnum::ACCEPTED]);
        $reservation->reservable->user->notify(new PatientAcceptScheduleReservationNotification($reservation));
        return Api::isOk('done');
    }

    /**
     * @param Reservation $reservation
     * @param Reservation $shared_reservation
     * @return Core
     */
    public function toggleShare(Reservation $reservation, Reservation\ItemsLine $analysis): Core {
        $reservation->sharedAnalysis()->toggle([$analysis->id]);
        return Api::isOk("Done");
    }

    public function join(Reservation $reservation) {
        $join = request()->get('confirmed', 1);
        $reservation->generateVoiceCall();
        if ($join) {
            $column = $reservation->user_id == auth()->id() ? 'customer_start_at' : 'contractor_start_at';
            $reservation->conversation()->update(["is_started" => 1, $column => now()]);
            $reservation->update(['status' => ReservationStatus::PROCESSING]);

        }

//        dd($reservation->conversation->patient->token);
        if ($reservation->conversation->startedByContractor() && !$reservation->conversation->startedByCustomer()) {
            $customerDeviceToken = $reservation->patient->deviceTokens()->first();

            if ($customerDeviceToken->voip_token) {
                VoipNotification::make()->send($customerDeviceToken->voip_token, "New reservation", "Reservation $reservation->id", [
                    'displayName' => $reservation->reservable->name,
                    'number' => $reservation->reservable_id,
                    'handle' => $reservation->reservable_id,
                    'reservation_id' => $reservation->id
                ]);
            } else {

                Firebase::make()->setTokens([$customerDeviceToken->token])->setMoreData([
                    "content_available" => true,
                    "number" => $reservation->reservable_id,
                    "call_type" => "voice",
                    "doctor_name" => $reservation->reservable->name,
                    "reservation_id" => $reservation->id

                ]);
            }


        }
        return Api::isOk(__("Joined"))->setData(
            array_merge(['agora_token' => $reservation->conversation->token,], $reservation->generateChatTokens())
        );
    }


    public function left(Reservation $reservation) {
        $column = $reservation->user_id == auth()->id() ? 'customer_end_at' : 'contractor_start_at';
        $reservation->conversation()->update([$column => now()]);
        return Api::isOk(__("Has left"));
    }

    public function end(Reservation $reservation) {

        if ($reservation->user_id == auth()->id()) {
            $reservation?->conversation()?->update(["finished_at" => now()]);
        }
        $reservation->update(['status' => ReservationStatus::COMPLETED]);

        return Api::isOk(__("Session end"));
    }
}
