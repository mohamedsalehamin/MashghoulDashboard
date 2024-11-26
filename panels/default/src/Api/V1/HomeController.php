<?php

namespace App\DefaultPanel\Api\V1;

use Api;
use App\AdminPanel\Http\Controllers\Controller;
use App\Api\Core;
use App\Ecommerce\Http\Resources\Api\Customer\SliderResource;
use App\Sliders\Model\Slider;

class HomeController extends Controller {


    public function slider(): Core {
        $sliders = Slider::active()->latest()->get();

        return Api::isOk(__("List of sliders"), SliderResource::collection($sliders));
    }


}
