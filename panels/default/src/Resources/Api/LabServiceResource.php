<?php

namespace App\DefaultPanel\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class LabServiceResource extends JsonResource {

    public function toArray($request) {

        return [
            'id' => $this->id,
            'name' => $this->name[app()->getLocale()],
            'description' => $this->description[app()->getLocale()],
            'price' => $this->price->format(),
            'sale_price' => $this->sale_price->format(),
            'final_price'=>$this->sale_price->isZero()?$this->price->format():$this->sale_price->format(),

        ];
    }


}
