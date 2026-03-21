<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkingTimesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'day_name' => $this['day_name'],
            'day' => __("forms.fields.weekdays." . $this['day_name']),
            'from' => !empty($this['from']) ? Carbon::parse($this['from'])->locale(app()->getLocale())->translatedFormat('h:i A') : '',
            'to' => !empty($this['to']) ? Carbon::parse($this['to'])->locale(app()->getLocale())->translatedFormat('h:i A') : '',
        ];
    }
}
