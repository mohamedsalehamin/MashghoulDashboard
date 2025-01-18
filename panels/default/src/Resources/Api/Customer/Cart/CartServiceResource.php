<?php

namespace App\DefaultPanel\Resources\Api\Customer\Cart;

use Arr;
use Cknow\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CartServiceResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $title = $this->associatedModel->title;
        if (is_array($title)) {
            $title = $title[app()->getLocale()] ?? $title[app()->getLocale() == 'ar' ? 'en' : 'ar'];
        }
        $products=Arr::get($this->attributes, 'products', []);
        return [
            'id' => $this->associatedModel->id,
            'image' => $this->associatedModel->getFirstMediaUrl(),
            "name" => $title ?? '',
            "quantity" => $this->quantity,
            'products' => CartProductResource::collection($products),
            'service_price'=>$this->associatedModel->price->format(),
            'products_price'=>Money::parse(collect($products)->reduce(fn($carry, $product) => $carry +($product->price->formatByDecimal() *$product->quantity),0))->format(),
            'total' => Money::parse($this->getPriceSumWithConditions())->format()
        ];
    }

}
