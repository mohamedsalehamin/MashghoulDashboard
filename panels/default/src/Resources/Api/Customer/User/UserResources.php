<?php

namespace App\DefaultPanel\Resources\Api\Provider\User;

use Illuminate\Http\Resources\Json\JsonResource;
use function App\Http\Resources\Api\Customer\User\setting_user;
use function App\Http\Resources\Api\Customer\User\upload_storage_url;

class UserResources extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => [
                "id" => $this->city?->id,
                "name" => $this->city?->name,
                "lat" => $this->city->latitude,
                "lng" => $this->city->longitude
            ],
            'gender' => $this->gender,
            'avatar' => (!is_null($this->avatar)) ? upload_storage_url($this->avatar) : "https://www.gravatar.com/avatar/dfgh",
            'api_token' => $this->api_token,
            'notification_status' => setting_user()?->bool("notification_status", 1) ?? 1,
        ];
    }
}
