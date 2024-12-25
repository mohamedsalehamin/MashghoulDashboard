<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use App\DefaultPanel\Resources\Api\Provider\RateResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource {

    public function toArray($request): array {
        return [
            'id' => $this->id,
            'created_date' => $this->created_at->format('Y-m-d H:i:s'),
            'provider' => LightProviderResource::make($this->reservable),
            'seat' => $this->seat?->title,
            'status' => $this->status->getLabel(),
            'duration' => $this->as_cart->getContent()->sum(fn($service) => $service->associatedModel->duration),
            'services' => ReservationServiceResource::collection($this->as_cart->getContent()),
            'coupon'=>ReservationConditionsResource::make($this->as_cart->getConditions()->values()->filter(fn($condition)=>$condition?->getType()=='coupon')?->first()),
            'date' => $this->date->format('Y-m-d'),
            'from' => Carbon::parse($this->from)->format('H:i'),
            'to' => Carbon::parse($this->to)->format('H:i'),
            $this->mergeWhen($this->rate()->exists(), [
                'rates' => RateResource::collection($this->rates),
            ]),

            'can' => [
                'rate' => $this->canRate(),
            ],
            'enums' => [
                'status' => $this->status,
            ],
            'points'=>$this->meta_data['points']??'',
            'invoice_url' => route('reservations.invoice', $this),
            'transactions' => ReservationTransactionResource::collection($this->transactions),
            'totals' => $this->as_cart->formattedTotals()
        ];
    }


}
