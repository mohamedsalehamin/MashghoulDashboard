<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use App\DefaultPanel\Settings\GeneralSettings;
use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource {

    public function toArray($request) {
        $services = $this->relationLoaded('services') ? $this->services : $this->services()->enabled()->get();
        $serviceGroups = $this->relationLoaded('serviceGroups')
            ? $this->serviceGroups->sortBy('sort')->sortBy('id')->values()
            : $this->serviceGroups()->orderBy('sort')->orderBy('id')->get();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'service_groups' => $serviceGroups->map(fn($g) => [
                'id' => $g->id,
                'title' => $g->getTranslations('title')[app()->getLocale()],
            ])->values()->all(),
            'services' => ServiceResource::collection($services),
            'working_days' => WorkingTimeSlotsResource::collection(
                GeneralSettings::normalizeSeatDaysListToShifts($this->meta_data['days_list'] ?? [])
            ),
        ];
    }
}
