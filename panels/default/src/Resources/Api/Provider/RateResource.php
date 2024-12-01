<?php

namespace App\DefaultPanel\Resources\Api\Provider;

use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource {

    public function toArray($request): array {

        return [
            'id' => $this->id,
            'rate' => $this->rate,
            'comment' => $this->comment,
            'type' => $this->type,
        ];
    }


}
