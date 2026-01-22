<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use App\DefaultPanel\Resources\Api\AdminResource;
class WithdrawalRequestTimelineResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'title' => $this->title[app()->getLocale()],
            'changed_by' => AdminResource::make($this->whenLoaded('changedBy')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
} 