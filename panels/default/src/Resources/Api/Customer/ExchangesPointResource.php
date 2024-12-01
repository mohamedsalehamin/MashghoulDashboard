<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use App\ContentModule\Resources\LevelResource;
use App\DefaultPanel\Lib\Utils;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangesPointResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->id,
            'plan' =>$this->plan?->title,
            'points' =>$this->points,
            'price' =>$this->price->format(),
            'expired_at' => $this->expired_at->format("Y-m-d H:i a"),
            'created_at' => $this->created_at->format("Y-m-d H:i a"),
        ];
    }
}
