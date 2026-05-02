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

        $dateRaw = $t['date'] ?? null;
        $dateIso = null;
        if ($dateRaw !== null && $dateRaw !== '') {
            try {
                $dateIso = Carbon::parse($dateRaw)->toIso8601String();
            } catch (\Throwable) {
                $dateIso = is_string($dateRaw) ? $dateRaw : null;
            }
        }

        return [
            'name_ar' => $t['name_ar'] ?? null,
            'name_en' => $t['name_en'] ?? null,
            'text_ar' => $t['text_ar'] ?? null,
            'text_en' => $t['text_en'] ?? null,
            'rating' => (int) ($t['rating'] ?? 5),
            'date' => $dateIso,
            'type' => $type,
            'avatar_url' => $avatarUrl,
            'media_url' => $mediaUrl,
        ];
    }
}
