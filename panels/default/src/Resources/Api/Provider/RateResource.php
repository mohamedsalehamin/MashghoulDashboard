<?php

namespace App\DefaultPanel\Resources\Api\Provider;

use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource {

    public function toArray($request): array {

        return [
            'id' => $this->id,
            'rate' => $this->rate,
            'comment' => $this->comment,
            'type' => $this->type,
            'replies' => $this->when(
                $this->relationLoaded('replies') && $this->replies->isNotEmpty(),
                function() {
                    return $this->replies->map(function($reply) {
                        return [
                            'id' => $reply->id,
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
