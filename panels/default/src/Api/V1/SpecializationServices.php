<?php

namespace App\DefaultPanel\Api\V1;

use App\CatalogModule\Models\Specialization;
use App\DefaultPanel\Resources\Api\SpecializationResource;
use App\DefaultPanel\Resources\Api\SpecializationWithChildrenResource;
use Tasawk\Api\Facade\Api;

class SpecializationServices  {
    public function list() {
        $specializations = Specialization::parent()->enabled()->latest()->get();
        return Api::isOk('List of specialization', SpecializationResource::collection($specializations));
    }

    public function show(Specialization $specialization) {
        return Api::isOk(__("Category information"),  SpecializationWithChildrenResource::make($specialization));
    }


}
