<?php

namespace App\DefaultPanel\Api\V1\Customer;

use App\CatalogModule\Models\Branch;
use App\CatalogModule\Models\CancellationReason;
use App\CatalogModule\Models\ContactType;
use App\CatalogModule\Models\ReportReason;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Resources\Api\Provider\CancellationReasonResource;
use App\DefaultPanel\Settings\GeneralSettings;
use Illuminate\Support\Collection;
use Str;
use Tasawk\Api\Facade\Api;


class SettingServices {
    public function all(GeneralSettings $settings) {
        return Api::isOk(__("Settings list"), [
            "name" => $settings->app_name,
            'email' => $settings->app_email,
            'address' => $settings->app_address,
            "phone" => $settings->app_phone,
            "whatsapp" => $settings->app_whatsapp,
            'social_media' => $this->socialMedia($settings->social_links),
        ]);
    }


    public function socialMedia($links): Collection {
        return collect($links)->map(function ($el) {
            $el['id'] = Str::between($el['icon'], '-', '-');
            return $el;
        })->pluck('link', 'id');

    }




    public function reservationStatuses() {
        foreach (ReservationStatus::cases() as $key => $value) {
            $data[] = [
                'key' => $value,
                'value' => $value->getLabel(),
            ];
        }
        return Api::isOk("Statuses List")->setData(collect($data));
    }


}
