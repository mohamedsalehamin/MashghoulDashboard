<?php

namespace App\DefaultPanel\Resources\Api\Customer\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationsResources extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {

        return [
            'id' => $this->id,
            "title" => $this->data['title'][app()->getLocale()] ?? $this->data['title'] ?? "",
            "description" => $this->data['description'][app()->getLocale()] ?? $this->data['description'] ?? "",
            'created_date' => $this->created_at,
            'data' => (object)($this->data['data'] ?? []),
            'read_at' => $this->read_at,
        ];
    }

}
