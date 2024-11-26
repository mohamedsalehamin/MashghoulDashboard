<?php

namespace App\DefaultPanel\Resources\Api\Labs;


use App\DefaultPanel\Resources\Api\LightLabResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationPrescriptionResource extends JsonResource {

    public function toArray($request): array {

        return [
            'id' => $this->id,
            'date' => $this->created_at->format('Y-m-d H:i:s'),
            'diagnosis' => $this->diagnosis,
            'medicines' => ReservationPrescriptionItemResource::collection($this->items->where('type', 'medicine')),
            'rays' => ReservationPrescriptionItemResource::collection($this->items->where('type', 'ray')),
            'tests' => ReservationPrescriptionItemResource::collection($this->items->where('type', 'test')),

        ];
    }


}
