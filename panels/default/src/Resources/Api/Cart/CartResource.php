<?php

namespace App\DefaultPanel\Resources\Api\Cart;

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
            'duration'=>$this->getContent()->max(fn($product)=>$product->associatedModel->duration),
            'duration_unit'=>'minutes',
            "products" => CartProductResource::collection($this->getContent()->values()),
            'totals' => $this->formattedTotals()
        ];
    }

}
