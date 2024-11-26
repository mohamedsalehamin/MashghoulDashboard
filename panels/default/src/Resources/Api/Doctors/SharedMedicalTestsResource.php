<?php

namespace App\DefaultPanel\Resources\Api\Doctors;

use App\ContentModule\Models\Post;
use App\DefaultPanel\Resources\Api\DoctorServiceResource;
use App\DefaultPanel\Resources\Api\LightArticleResource;
use App\DefaultPanel\Resources\Api\LightDoctorResource;
use App\DefaultPanel\Resources\Api\LightLabResource;
use App\UsersModule\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource;

class SharedMedicalTestsResource extends JsonResource {

    public function toArray($request): array {
        return [
            'id' => $this->id,
            'reservation'=>[
                'id' => $this->reservation->id,
                'date'=>$this->reservation->date->format("Y-m-d")
            ],
            'attachment' => $this->getFirstMediaUrl(),

            'name' => $this->model['name'][app()->getLocale()],
            'lab'=>LightLabResource::make($this->reservation->reservable),

        ];
    }


}
