<?php

namespace App\DefaultPanel\Api\V1\Customer\Profile;


use App\DefaultPanel\Resources\Api\Provider\LightProviderResource;
use App\UsersModule\Models\Provider;
use Tasawk\Api\Core;
use Tasawk\Api\Facade\Api;

class FavoriteService {

    public function index(): Core {
        return Api::isOk(__("fav list"), LightProviderResource::collection(auth()->user()->favorite(Provider::class)->paginate(15)));

    }
}
