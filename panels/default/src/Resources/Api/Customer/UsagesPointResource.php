<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Request;
use App\ContentModule\Resources\LevelResource;
use App\DefaultPanel\Lib\Utils;
use App\DefaultPanel\Resources\Api\Provider\LightReservationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UsagesPointResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'reservation_id' =>$this->reservation?->id,
            'reservation_number' =>$this->reservation?->reservation_number,
            'price' =>$this->price,
            'created_at' => $this->created_at->format("Y-m-d H:i a"),
        ];
    }
}
