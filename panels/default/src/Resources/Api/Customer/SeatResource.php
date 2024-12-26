<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'services' => ServiceResource::collection($this->services()->enabled()->get()),
            'working_days'=>WorkingTimesResource::collection(collect( $this->meta_data['days_list'])->where('status',1))
        ];
    }
}
