<?php

namespace App\DefaultPanel\Api\V1\Customer\Content;

use App\ContentModule\Models\Banner;
use App\DefaultPanel\Resources\Api\Customer\BannerResource;
use Tasawk\Api\Facade\Api;

class BannerServices  {
    public function list() {
        $banners = Banner::enabled()
            ->orderBy('sort')
            ->get();
        return Api::isOk(__("List of banners"), BannerResource::collection($banners));
    }




}
