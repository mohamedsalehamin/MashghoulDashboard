<?php

namespace App\DefaultPanel\Api\V1;

use App\ContentModule\Models\Category;
use App\DefaultPanel\Resources\Api\CategoryResource;
use App\DefaultPanel\Resources\Api\CategoryWithChildrenResource;
use App\DefaultPanel\Resources\Api\DoctorResource;
use App\DefaultPanel\Resources\Api\LightProviderResource;
use App\DefaultPanel\Resources\Api\ProviderResource;
use App\DefaultPanel\Resources\Api\SeatResource;
use App\UsersModule\Models\Provider;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Tasawk\Api\Facade\Api;


class ProvidersServices {
    public function index() {
        $user_location = [
            'lat' => request()->get('latitude', 0),
            'lng' => request()->get('longitude', 0),
        ];
        $providers = Provider::when(request()->filled('term'), fn($query) => $query->where('name', 'like', '%' . request('term') . '%'))
            ->when(request()->filled('city_id'), fn($query) => $query->where('city_id', request('city_id')))
            ->when(request()->filled('category_id'), fn($query) => $query->where('category_id', request('category_id')))
            ->when(request()->get('order_by') == 'nearest', fn($query) => $query->orderBy('distance', 'asc'))
            ->when(request()->get('order_by') == 'farthest', fn($query) => $query->orderBy('distance', 'desc'))
            ->when(request()->get('order_by') == 'rating', fn($query) => $query->withAvg('rate', 'rate')->orderBy('rate_avg_rate', request()->get('order_dir', 'desc')))
            ->when(request()->get('order_by') == 'date', fn($query) => $query->orderBy('created_at', request()->get('order_dir', 'desc')))
            ->withDistanceSphere('location', new Point($user_location['lat'], $user_location['lng']))
            ->get();

        return Api::isOk(__("Providers List"), LightProviderResource::collection($providers));
    }

    public function show(Provider $provider) {
        $user_location = [
            'lat' => request()->get('latitude', 0),
            'lng' => request()->get('longitude', 0),
        ];
        $provider = Provider::withDistanceSphere('location', new Point($user_location['lat'], $user_location['lng']))
            ->where('id', $provider->id)->first();
        return Api::isOk(__("provider information"), ProviderResource::make($provider));
    }

    public function seats(Provider $provider) {
        return Api::isOk(__("provider information"), SeatResource::collection($provider->seats));
    }

    public function toggleFavorite(Provider $provider) {
        $provider->toggleFavorite();
        return Api::isOk(__("provider information"), ProviderResource::make($provider));
    }

}
