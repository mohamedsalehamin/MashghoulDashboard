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
        $regularPrice = $this->associatedModel->price->getAmount();
        $salePrice = $this->associatedModel->sale_price->getAmount();

        $discountPercentage = false;
        if (!$this->associatedModel->sale_price->isZero() && $regularPrice > 0) {
            $discountPercentage = round(($regularPrice - $salePrice) / $regularPrice * 100);
        }
        return [
            'id' => $this->associatedModel->id,
            'image' => $this->associatedModel->getServiceImageUrl(),
            "name" => $title ?? '',
            "quantity" => $this->quantity,
            'products' => CartProductResource::collection($products),
            'service_price'=>$this->associatedModel->price->format(),
            'sale_price' => !$this->associatedModel->sale_price->isZero() ? $this->associatedModel->sale_price->format() : false,
            'discount_percentage' => $discountPercentage,
            'products_price' => Money::parse(collect($products)->reduce(fn($carry, $product) => $carry + ((!$product->sale_price->isZero() ? $product->sale_price->formatByDecimal() : $product->price->formatByDecimal()) * $product->quantity), 0))->format(),
            'total' => Money::parse($this->getPriceSumWithConditions())->format()
        ];
    }

}
