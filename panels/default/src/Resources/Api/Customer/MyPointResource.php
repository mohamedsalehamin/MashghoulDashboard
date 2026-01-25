<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Request;
use App\DefaultPanel\Lib\Utils;
use Illuminate\Http\Resources\Json\JsonResource;

class MyPointResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'description' =>$this->meta_data['description'][app()->getLocale()]??null,
            'created_at' => $this->created_at->format("Y-m-d H:i a"),
        ];
    }
}
