<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource {

    public function toArray($request) {

        return [
            'id' => $this->id,
            'image' => $this->getFirstMediaUrl(app()->getLocale()),
        ];
    }


}
