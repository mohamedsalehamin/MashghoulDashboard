<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Request;
use App\DefaultPanel\Lib\Utils;
use Illuminate\Http\Resources\Json\JsonResource;

class PointResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {

        return [
            'id' => $this->id,
            'title' =>$this->title,
            'value'=>$this->value,
            'price'=>$this->price,
            'can_exchange'=>$this->canExchangeByUser(request()->user('sanctum')),
        ];
    }
}
