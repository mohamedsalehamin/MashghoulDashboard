<?php

namespace App\DefaultPanel\Actions;

use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Models\Transaction;
use Lorisleiva\Actions\Concerns\AsAction;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;

class RefundTransaction {
    use AsAction;

    public function handle(Reservation $reservation) {
        if ($reservation->transaction?->meta_data['gateway'] === 'wallet') {
            $amount = $reservation->transaction->price->formatByDecimal();
        } else {
            $amount = $reservation->price->formatByDecimal();
        }
        $reservation->customer?->deposit($amount, [
            'description' => [
                'ar' => __("panel.messages.refund_reservation", ['no' => $reservation->id,'amount'=>$amount], 'ar'),
                'en' => __("panel.messages.refund_reservation", ['no' => $reservation->id,'amount'=>$amount], 'en'),
            ]
        ]);
    }


}
