<?php

namespace App\DefaultPanel\Api\V1\Customer;

use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
use App\DefaultPanel\Resources\Api\Provider\CityResource;
use App\DefaultPanel\Resources\Api\Provider\StateResource;
use Tasawk\Api\Facade\Api;

class LocationServices {
    public function countries() {
        return Api::isOk("Countries list", StateResource::collection(Country::enabled()->get()));
    }

    public function states(Country $country) {
        return Api::isOk("States list", StateResource::collection($country->states()->enabled()->get()));

    }

    public function cities(State $state) {
        return Api::isOk("Cities list", CityResource::collection($state->cities()->enabled()->get()));
    }


}
