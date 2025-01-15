<?php

namespace App\DefaultPanel\Api\V1\Customer\Content;

use App\ContentModule\Models\Banner;
use App\ContentModule\Models\Slider;
use App\DefaultPanel\Resources\Api\Customer\BannerResource;
use App\DefaultPanel\Resources\Api\Customer\SliderResource;
use Tasawk\Api\Facade\Api;

class SliderServices  {
    public function list() {
        $sliders = Slider::enabled()
            ->orderBy('sort')
            ->get();
        return Api::isOk(__("List of banners"), SliderResource::collection($sliders));
    }




}
