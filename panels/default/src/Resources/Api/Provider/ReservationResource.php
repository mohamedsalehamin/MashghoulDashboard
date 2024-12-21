<?php

namespace App\DefaultPanel\Resources\Api\Provider;

use App\DefaultPanel\Resources\Api\Customer\LightProviderResource;
use App\DefaultPanel\Resources\Api\Customer\ReservationConditionsResource;
use App\DefaultPanel\Resources\Api\Customer\ReservationTransactionResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource {

    public function toArray($request): array {
        return [
            'id' => $this->id,
            'reservation_number' => $this->reservation_number,
            'created_date' => $this->created_at->format('Y-m-d H:i:s'),
            'customer' => CustomerResource::make($this->customer),
            'seat' => $this->seat?->title,
            'status' => $this->status->getLabel(),
            'duration' => $this->as_cart->getContent()->sum(fn($service) => $service->associatedModel->duration),
            'services' => ReservationServiceResource::collection($this->as_cart->getContent()),
            'coupon'=>ReservationConditionsResource::make($this->as_cart->getConditions()->values()->filter(fn($condition)=>$condition?->getType()=='coupon')?->first()),
            'date' => $this->date->format('Y-m-d'),
            'from' => Carbon::parse($this->from)->format('H:i'),
            'to' => Carbon::parse($this->to)->format('H:i'),
            'logs' => ReservationLogResource::collection($this->timeline),
            $this->mergeWhen($this->rate()->exists(), [
                'rates' => RateResource::collection($this->rates),
            ]),

            'can' => [
                'rate' => $this->canRate(),
            ],
            'enums' => [
                'status' => $this->status,
                'payment_status' => $this->getPaymentStatus(),
            ],
            'invoice_url' => route('reservations.invoice', $this),
            'transactions' => ReservationTransactionResource::collection($this->transactions),
            'points' => $this->meta_data['points'] ?? 0,
            'totals' => $this->as_cart->formattedTotals()
        ];
    }


}
