<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Models\Transaction;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Notifications\ReservationCreatedSuccessfullyNotification;
use Lorisleiva\Actions\Concerns\AsAction;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;
use Notification;

class OrderPaidAction {
    use AsAction;

    public function handle(Reservation $reservation) {
        $reservation->addTimeline([
            'ar' => __('panel.messages.reservation_created_successfully', [], 'ar'),
            'en' => __('panel.messages.reservation_created_successfully', [], 'en')
        ], 'created');
        Notification::send([...\App\DefaultPanel\Lib\Utils::getAdministrationUsers(), $reservation->reservable->user], new ReservationCreatedSuccessfullyNotification($reservation));
        $reservation->customer->notify(new \App\Notifications\WiningGiftSuccessfullyNotification([
            'ar' => __("panel.messages.you_are_gain_points_for_reservation", ['points' => GeneralSettings::getPointsOnAction('reserve'), 'id' => $reservation->id], 'ar'),
            'en' => __("panel.messages.you_are_gain_points_for_reservation", ['points' => GeneralSettings::getPointsOnAction('reserve'), 'id' => $reservation->id], 'en'),
        ]));
    }

}
