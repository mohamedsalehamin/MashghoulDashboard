<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource {

    public function toArray($request) {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'services' => ServiceResource::collection($this->services()->enabled()->get()),

            'working_days' => collect(WorkingTimeSlotsResource::collection(collect( $this->meta_data['days_list']??[])->map(fn($slot)=>collect($slot)->where('status',1))))->filter(fn($slot)=>$slot->isNotEmpty())->values(),
        ];
    }
}
