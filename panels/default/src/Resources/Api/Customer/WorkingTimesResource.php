<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class   WorkingTimesResource extends JsonResource {

    public function toArray($request) {
        return [
            'day_name' => $this['day_name'],
            'day' => __("forms.fields.weekdays.".$this['day_name']),
            'from' => $this['from'],
            'to' => $this['to'],
        ];
    }
}
