<?php

namespace App\DefaultPanel\Resources\Api\Customer\Cart;

use App\DefaultPanel\Resources\Api\Customer\Cart\CartServiceResource;
use App\DefaultPanel\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CartResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'duration' => $this->getContent()->sum(fn($product) => $product->associatedModel->duration),
            'duration_unit' => 'minutes',
            'complete_order_text' => $request->route('provider')->user?->options?->texts[app()->getLocale()]['text_when_order_completed'] ?? '',
            'reservation_flow' => $request->route('provider')?->user?->options?->reservation_flow ?? 'total',
            "services" => CartServiceResource::collection($this->getContent()->values()),
            'points' => GeneralSettings::getPointsOnAction('reserve'),
            'totals' => $this->formattedTotals()
        ];
    }

}
