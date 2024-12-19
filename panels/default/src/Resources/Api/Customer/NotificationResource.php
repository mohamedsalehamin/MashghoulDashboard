<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array {

        return [
            'id' => $this->id,
            "title" => $this->title,
            "description" => $this->body,
            'created_date' => $this->created_at->toDateTimeString(),
            'formatted_date' => $this->created_at->format("Y-m-d h:i a"),
            'data' => isset($this->data['viewData']) ? (object)$this->data['viewData'] : (object)[],
            'read_at' => $this->read_at?->format("Y-m-d h:i a"),
        ];
    }
}
