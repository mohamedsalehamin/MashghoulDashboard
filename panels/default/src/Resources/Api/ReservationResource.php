<?php

namespace App\DefaultPanel\Resources\Api;

use App\DefaultPanel\Resources\Api\Cart\CartServiceResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource {

    public function toArray($request): array {
        $payment_data = $this->transaction?->meta_data;
        return [
            'id' => $this->id,
            'created_date' => $this->created_at->format('Y-m-d H:i:s'),
            'provider' => LightProviderResource::make($this->reservable),
            'seat' => $this->seat?->title,
            'status' => $this->status->getLabel(),
            'duration' => $this->as_cart->getContent()->sum(fn($service) => $service->associatedModel->duration),
            'services' => ReservationServiceResource::collection($this->as_cart->getContent()),
            'date' => $this->date->format('Y-m-d'),
            'from' => Carbon::parse($this->from)->format('H:i'),
            'to' => Carbon::parse($this->to)->format('H:i'),
//            $this->mergeWhen($this->rate()->exists(), [
//                'rating' => [
//                    'rate' => $this->rate?->rate,
//                    'comment' => $this->rate?->comment,
//                    'date' => $this->rate?->created_at->format("Y-m-d")
//                ]
//            ]),

            'can' => [
                'rate' => $this->canRate(),
            ],
            'enums' => [
                'status' => $this->status,
            ],
            'invoice_url' => route('reservations.invoice', $this),
            'transaction' => [
                'price' => $this->price->format(),
                'gateway' => $payment_data['gateway'] ?? '',
                'invoiceId' => $payment_data['invoiceId'] ?? '',
                'invoiceURL' => $payment_data['invoiceURL'] ?? '',
                'paid_at' => isset($payment_data['paid_at']) ? Carbon::parse($payment_data['paid_at'])->timezone('africa/cairo')->format("Y-m-d h:i a") : null
            ],
            'totals' => $this->as_cart->formattedTotals()
        ];
    }


}
