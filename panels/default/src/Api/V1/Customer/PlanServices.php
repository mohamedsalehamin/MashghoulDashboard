<?php

namespace App\DefaultPanel\Api\V1\Customer;


use App\ContentModule\Models\Level;
use App\ContentModule\Models\Point;
use App\DefaultPanel\Resources\Api\Customer\PointResource;
use Tasawk\Api\Facade\Api;

class PlanServices {

    public function index() {
        return Api::isOk(__("Points"), PointResource::collection(Level::enabled()->latest()->get()));
    }


}
