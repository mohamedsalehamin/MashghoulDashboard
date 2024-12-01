<?php

namespace App\DefaultPanel\Resources\Api\Provider;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LightReservationResource extends JsonResource {

    public function toArray($request): array {
        return [
            'id' => $this->id,
            'reservation_number' => $this->reservation_number,
            'created_date' => $this->created_at->format('Y-m-d H:i:s'),
            'customer' => CustomerResource::make($this->customer),
            'seat' => $this->seat?->title,
            'status' => $this->status->getLabel(),
            'duration' => $this->as_cart->getContent()->sum(fn($service) => $service->associatedModel->duration),
            'date' => $this->date->format('Y-m-d'),
            'from' => Carbon::parse($this->from)->format('H:i'),
            'to' => Carbon::parse($this->to)->format('H:i'),
            'enums' => [
                'status' => $this->status,
            ],
        ];
    }


}
