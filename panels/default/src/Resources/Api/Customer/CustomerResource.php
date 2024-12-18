<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use App\DefaultPanel\Enum\GenderEnum;
use App\Models\PointsExchange;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->id,
            'avatar' => $this->getFirstMediaUrl(),
            'name' => $this->name,
            "first_name" => $this->data['first_name'] ?? '',
            "last_name" => $this->data['last_name'] ?? '',
            'email' => $this->email,
            'phone' => $this->phone,
            'dob' => $this->dob,
            'api_token' => $this->api_token,
            'country' => CountryResource::make($this?->city?->state?->country),
            'state' => StateResource::make($this->city?->state),
            'city' => CityResource::make($this->city),
            'gender' => GenderEnum::from($this->gender)->getLabel(),
            'gender_enum' => GenderEnum::from($this->gender)->value,
            'balance' =>$this->getTotalPointsBalance(),
            'points'=>$this->getTotalPoints(),
            'notification_status' => $this->settings['notification_status'] ?? 1,
            'unread_notifications_count' => $this->unreadNotifications()->count(),
            'preferred_language' => $this->settings['preferred_language'] ?? 'ar',
            "phone_verified" => (int)!is_null($this->phone_verified_at),
        ];
    }
}
