<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use App\DefaultPanel\Resources\Api\Provider\RateResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationTransactionResource extends JsonResource {

    public function toArray($request): array {
        $payment_data = $this->meta_data;

        return [
            'price' => $this->price->format(),
            'gateway' => $payment_data['gateway'] ?? '',
            'invoiceId' => $payment_data['invoiceId'] ?? '',
            'invoiceURL' => $payment_data['invoiceURL'] ?? '',
            'paid_at' => isset($payment_data['paid_at']) ? Carbon::parse($payment_data['paid_at'])->timezone('africa/cairo')->format("Y-m-d h:i a") : null
        ];
    }


}
