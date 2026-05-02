<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $t = is_array($this->resource) ? $this->resource : (array) $this->resource;

        $type = $t['type'] ?? 'text';
        $mediaPath = $t['media'] ?? null;
        $mediaUrl = null;
        if (in_array($type, ['image', 'video', 'audio'], true) && ! empty($mediaPath)) {
            $mediaUrl = asset('storage/'.ltrim((string) $mediaPath, '/'));
        }

        $avatarUrl = null;
        if (! empty($t['avatar'])) {
            $avatarUrl = asset('storage/'.ltrim((string) $t['avatar'], '/'));
        }

       
        return [
            'name' => $t['name_' . app()->getLocale()] ?? null,
            'text' => $t['text_' . app()->getLocale()] ?? null,
            'rating' => (int) ($t['rating'] ?? 5),
            'date' => Carbon::parse($t['date'])->translatedFormat('M j, Y') ?? null,
            'type' => $type,
            'avatar_url' => $avatarUrl,
            'media_url' => $mediaUrl,
        ];
    }
}
