<?php

namespace App\DefaultPanel\Resources\Api\Orders;

use App\DefaultPanel\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LightOrderResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $cart = $this->as_cart;
        $settings = new GeneralSettings();
        return [
            "id" => $this->id,
            "order_number" => $this->order_number,
            "status" => __("panel.enums." . $this->status->value),
            "status_code" => $this->status,
            "date" => $this->date?->format("Y-m-d") ?? __("Not yet determined"),
            "totals" => $cart->formattedTotals()['total'],
        ];
    }
}
