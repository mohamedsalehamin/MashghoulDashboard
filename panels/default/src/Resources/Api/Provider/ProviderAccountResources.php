<?php

namespace App\DefaultPanel\Resources\Api\Provider;

use Illuminate\Http\Request;
use App\DefaultPanel\Enum\GenderEnum;

use Illuminate\Http\Resources\Json\JsonResource;


class ProviderAccountResources extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {

        return [
            'id' => $this->id,
            'avatar' => $this->getFirstMediaUrl(),
            'name' => $this->name,
            "first_name"=>$this->data['first_name']??'',
            "last_name"=>$this->data['last_name']??'',
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => GenderEnum::from($this->gender)->getLabel(),
            'gender_enum' => GenderEnum::from($this->gender)->value,
            'provider'=>ProviderResources::make($this->provider),
            'api_token' => $this->api_token,
            'notification_status' => $this->settings['notification_status'] ?? 1,
            'unread_notifications_count'=>$this->unreadNotifications()->count(),
            'preferred_language' => $this->settings['preferred_language'] ?? 'ar',
            "phone_verified" => (int)!is_null($this->phone_verified_at),
        ];
    }
}
