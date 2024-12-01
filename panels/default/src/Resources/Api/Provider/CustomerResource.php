<?php

namespace App\DefaultPanel\Resources\Api\Provider;

use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Resources\Api\Customer\CityResource;
use App\DefaultPanel\Resources\Api\Customer\CountryResource;
use App\DefaultPanel\Resources\Api\Customer\StateResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->id,
            'avatar' => $this->getFirstMediaUrl(),
            'name' => $this->name,
            "first_name"=>$this->data['first_name']??'',
            "last_name"=>$this->data['last_name']??'',
            'email' => $this->email,
            'phone' => $this->phone,

        ];
    }
}
