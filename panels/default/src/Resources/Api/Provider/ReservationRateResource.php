<?php

namespace App\DefaultPanel\Resources\Api\Provider;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationRateResource extends JsonResource {

    public function toArray($request): array {

        return [
            'id' => $this->id,
            'reservation_number' => $this->reservation_number,
            'created_date' => $this->created_at->format('Y-m-d H:i:s'),
            'customer' => CustomerResource::make($this->customer),
            'rates'=>RateResource::collection($this->rates),
        ];
    }


}
