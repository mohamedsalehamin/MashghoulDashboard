<?php

namespace App\DefaultPanel\Resources\Api\Provider;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationLogResource extends JsonResource {

    public function toArray($request): array {

        return [
            'id' => $this->id,
            'title' => $this->title[app()->getLocale()],
            'created_date' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }


}
