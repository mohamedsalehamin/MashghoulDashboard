<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use App\DefaultPanel\Enum\GenderEnum;
use App\Models\PointsExchange;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'type' => $this->type,
            'description' => $this->meta['description'][app()->getLocale()]??'',
        ];
    }
}
