<?php

namespace App\Support;

use App\UsersModule\Models\Provider;

/**
 * Builds portfolio album payloads for web and API (aligned structures).
 */
class PortfolioAlbumPresentation
{
    public static function forProvider(Provider $provider): array
    {
        $locale = app()->getLocale();
        $albums = collect($provider->meta_data['portfolio_albums'] ?? []);
        $allMedia = $provider->getMedia('portfolio');

        if ($albums->isEmpty() && $allMedia->isNotEmpty()) {
            return [[
                'album_id' => null,
                'title' => __('site.heading.gallery'),
                'items' => $allMedia->map(fn ($m) => self::formatItem($m, null, $locale))->values()->all(),
            ]];
        }

        return $albums->map(function ($album) use ($allMedia, $locale) {
            $albumId = $album['album_id'] ?? null;
            $title = self::pickTranslated($album['title'] ?? [], $locale);
            $itemsMeta = collect($album['items'] ?? [])->filter(fn ($i) => is_array($i));

            if ($itemsMeta->isEmpty()) {
                $items = $allMedia
                    ->filter(fn ($m) => ($m->getCustomProperty('album_id') ?? '') === $albumId)
                    ->map(fn ($m) => self::formatItem($m, null, $locale))
                    ->values()
                    ->all();
            } else {
                $items = $itemsMeta->map(function (array $itemMeta) use ($allMedia, $albumId, $locale) {
                    $itemId = (string) ($itemMeta['item_id'] ?? '');
                    $media = $allMedia->first(fn ($m) => ($m->getCustomProperty('album_id') ?? '') === $albumId
                        && ($m->getCustomProperty('item_id') ?? '') === $itemId);

                    if (! $media) {
                        return null;
                    }

                    return self::formatItem($media, $itemMeta, $locale);
                })->filter()->values()->all();
            }

            return [
                'album_id' => $albumId !== null && $albumId !== '' ? (string) $albumId : null,
                'title' => $title,
                'items' => $items,
            ];
        })->filter(fn ($a) => ! empty($a['items']))->values()->all();
    }

    /**
     * @param  array<string, mixed>|null  $itemMeta  Row from meta_data.portfolio_albums[].items[]
     */
    private static function formatItem($media, ?array $itemMeta, string $locale): array
    {
        $mimeType = $media->mime_type ?? '';
        $type = str_starts_with($mimeType, 'video/') ? 'video'
            : (str_starts_with($mimeType, 'audio/') ? 'audio' : 'image');

        $title = '';
        $description = '';
        if ($itemMeta !== null) {
            $title = self::pickTranslated($itemMeta['title'] ?? [], $locale);
            $description = self::pickTranslated($itemMeta['description'] ?? [], $locale);
        }
        if ($title === '') {
            $t = $media->getCustomProperty('title');
            $title = is_array($t) ? self::pickTranslated($t, $locale) : (string) ($t ?? '');
        }
        if ($description === '') {
            $d = $media->getCustomProperty('description');
            $description = is_array($d) ? self::pickTranslated($d, $locale) : (string) ($d ?? '');
        }

        $rawAlbumId = $media->getCustomProperty('album_id');
        $rawItemId = $media->getCustomProperty('item_id');

        return [
            'id' => $media->id,
            'album_id' => ($rawAlbumId !== null && $rawAlbumId !== '') ? (string) $rawAlbumId : null,
            'item_id' => ($rawItemId !== null && $rawItemId !== '') ? (string) $rawItemId : null,
            'url' => $media->getFullUrl(),
            'type' => $type,
            'title' => $title,
            'description' => $description,
        ];
    }

    /**
     * @param  array<string, string>|mixed  $value
     */
    private static function pickTranslated(mixed $value, string $locale): string
    {
        if (! is_array($value)) {
            return is_string($value) ? $value : '';
        }

        return (string) ($value[$locale] ?? $value['ar'] ?? $value['en'] ?? '');
    }
}
