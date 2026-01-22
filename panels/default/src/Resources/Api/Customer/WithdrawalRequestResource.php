<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use App\DefaultPanel\Resources\Api\Customer\WithdrawalRequestTimelineResource;
class WithdrawalRequestResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'status' => $this->status->getLabel(),
            'enums' => [
                'status' => $this->status,
            ],
            'bank_details' => $this->bank_details,
            'rejection_reason' => $this->rejection_reason ? $this->rejection_reason[app()->getLocale()] : null,
            'transfer_amount' => $this->transfer_amount,
            'timeline' => WithdrawalRequestTimelineResource::collection($this->whenLoaded('timeline')),

            'created_date' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_date' => $this->updated_at->format('Y-m-d H:i:s'),
            
        ];
    }
} 