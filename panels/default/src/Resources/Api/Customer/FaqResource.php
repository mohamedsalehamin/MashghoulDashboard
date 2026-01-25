<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Request;
use App\DefaultPanel\Lib\Utils;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'question' =>Utils::getTranslatedField($this->question),
            'answer'=>Utils::getTranslatedField($this->answer)
        ];
    }
}
