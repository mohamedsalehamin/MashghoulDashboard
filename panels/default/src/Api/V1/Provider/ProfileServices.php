<?php

namespace App\DefaultPanel\Api\V1\Provider;


use Api;
use App\DefaultPanel\Actions\Shared\Authentication\UpdateUserPassword;
use App\DefaultPanel\Requests\Api\Provider\UpdatePasswordRequest;
use App\DefaultPanel\Resources\Api\Provider\ProviderAccountResources;
use App\DefaultPanel\Resources\Api\Provider\ReservationRateResource;


class ProfileServices {

    public function index() {
        return Api::isOk(__("Provider information"), ProviderAccountResources::make(auth()->user()));
    }

    public function updatePassword(UpdatePasswordRequest $request) {
        UpdateUserPassword::run(auth()->user(), $request->get('password'));
        return Api::isOk(__("Account information updated"), ProviderAccountResources::make(auth()->user()));
    }

    public function rates() {
        return Api::isOk(__("Provider rates"), ReservationRateResource::collection(provider()->reservations()->whereHas('rates')->paginate()))
            ->addAttribute('avg_rates', provider()->avgRate())
            ->addAttribute('rates_count', provider()->reservations()->whereHas('rates')->count());
    }
}
