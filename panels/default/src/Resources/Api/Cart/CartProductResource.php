<?php

namespace App\DefaultPanel\Resources\Api\Cart;

use Arr;
use Cknow\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CartProductResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $title = $this->associatedModel->title;
        if (is_array($title)) {
            $title = $title[app()->getLocale()] ?? $title[app()->getLocale()=='ar'?'en':'ar'];
        }
        return [
            'id' => $this->associatedModel->id,
            'image' => $this->associatedModel->image,

            "name" =>$title ??'',
            "quantity" => $this->quantity,
            'options' => CartProductOptionResource::collection(Arr::get($this->attributes, 'options', [])),
//            'price' => Money::parse($this->price)->format(),
//            'price' => Money::parse($this->getPriceWithConditions())->format(),
            'price_sum' => Money::parse($this->getPriceSumWithConditions())->format()
        ];
    }

}
