<?php

namespace App\DefaultPanel\Resources\Api\Labs;

use App\ContentModule\Models\Post;
use App\DefaultPanel\Resources\Api\DoctorServiceResource;
use App\DefaultPanel\Resources\Api\LightArticleResource;
use App\DefaultPanel\Resources\Api\LightDoctorResource;
use App\UsersModule\Models\Service;
use Cknow\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationServiceResource extends JsonResource {

    public function toArray($request): array {

        return [
            'id' => $this->model['name'][app()->getLocale()] ?? $this->name,
            'price' => Money::parse($this['price'])->format(),
            'file' => $this->getFirstMediaUrl()??null,

        ];
    }


}
