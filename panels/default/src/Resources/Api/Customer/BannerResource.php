<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource {

    public function toArray($request) {

        return [
            'id' => $this->id,
            'image' => $this->getFirstMediaUrl(app()->getLocale()),
            'link' => $this->link,
        ];
    }


}
