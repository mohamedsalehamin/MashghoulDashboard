<?php

namespace App\DefaultPanel\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientDataResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            'health_data' => PatientHealthDataResource::make($this->healthData),
            'diseases' => PatientChronicDiseasesDataResource::collection($this->chronicDiseases),
            'analysis' => $this->getMedia('analysis')->map(fn($media)=>[
                'id'=>$media['id'],
                'name'=>$media['name'],
                'url'=>$media->getUrl()
            ]),

        ];
    }
}
