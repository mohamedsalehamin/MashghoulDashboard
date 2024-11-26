<?php

namespace App\DefaultPanel\Resources\Api\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CartProductOptionResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        $name =  $this['name'];
        $value =  $this['value'];
        if (is_array($name)) {
            $name = $name[app()->getLocale()] ?? $name[app()->getLocale()=='ar'?'en':'ar'];
        }
        if (is_array($value)) {
            $value = $value[app()->getLocale()] ?? $value[app()->getLocale()=='ar'?'en':'ar'];
        }
        return [
            'id' => $this['id'],
            'name' => $name,
            'price' => $this['price'],
            'value' => $value,
            'value_id' => $this['value_id'],

        ];
    }

}
