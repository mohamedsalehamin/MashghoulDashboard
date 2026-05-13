<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource {

    public function toArray($request) {
        return [
            "name" => $this->reviewerDisplayName(),
            "rate" => (int) $this->rate,
            "comment" => $this->comment,
            "type" => $this->type,
            "created_at" => $this->created_at?->diffForHumans(),
            "replies" => $this->when(
                $this->relationLoaded('replies') && $this->replies->isNotEmpty(),
                function() {
                    return $this->replies->map(function($reply) {
                        return [
                            'comment' => $reply->comment,
                            'created_at' => $reply->created_at?->diffForHumans(),
                            'user' => $reply->user?->name ?? __('panel.provider'),
                        ];
                    });
                }
            ),
        ];
    }
}
