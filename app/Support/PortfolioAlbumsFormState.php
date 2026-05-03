<?php

namespace App\Support;

use App\UsersModule\Models\Provider;
use Illuminate\Support\Str;

/**
 * Normalizes portfolio_albums meta for Filament forms and derives legacy rows from media.
 */
class PortfolioAlbumsFormState
{
    /**
     * @param  array<int, mixed>  $albums
     * @return array<int, mixed>
     */
    public static function normalizeAlbums(?Provider $provider, array $albums): array
    {
        $allMedia = $provider?->getMedia('portfolio');

        return collect($albums)
            ->filter(fn ($item) => is_array($item))
            ->unique('album_id')
            ->map(function (array $album) use ($allMedia) {
                $albumId = $album['album_id'] ?? null;
                if (! $albumId) {
                    return $album;
                }

                $items = $album['items'] ?? null;
                if (is_array($items) && count($items) > 0) {
                    $album['items'] = collect($items)
                        ->filter(fn ($item) => is_array($item))
                        ->map(function (array $item) use ($albumId) {
                            $item['item_id'] = $item['item_id'] ?? Str::random(12);
                            $item['album_id'] = (string) ($item['album_id'] ?? $albumId);
                            $item['title'] = array_merge(
                                ['ar' => '', 'en' => ''],
                                is_array($item['title'] ?? null) ? $item['title'] : []
                            );
                            $item['description'] = array_merge(
                                ['ar' => '', 'en' => ''],
                                is_array($item['description'] ?? null) ? $item['description'] : []
                            );

                            return $item;
                        })
                        ->unique('item_id')
                        ->values()
                        ->all();

                    return $album;
                }

                if ($allMedia === null || $allMedia->isEmpty()) {
                    $album['items'] = [];

                    return $album;
                }

                $albumMedia = $allMedia->filter(fn ($m) => ($m->getCustomProperty('album_id') ?? '') === $albumId);

                $album['items'] = $albumMedia->map(function ($m) {
                    $itemId = $m->getCustomProperty('item_id');
                    if (! $itemId) {
                        $itemId = Str::random(12);
                        $m->setCustomProperty('item_id', $itemId);
                        $m->save();
                    }

                    $title = $m->getCustomProperty('title');
                    $titleAr = '';
                    $titleEn = '';
                    if (is_array($title)) {
                        $titleAr = (string) ($title['ar'] ?? '');
                        $titleEn = (string) ($title['en'] ?? '');
                    } elseif (is_string($title) && $title !== '') {
                        $titleAr = $title;
                        $titleEn = $title;
                    }

                    $description = $m->getCustomProperty('description');
                    $descAr = '';
                    $descEn = '';
                    if (is_array($description)) {
                        $descAr = (string) ($description['ar'] ?? '');
                        $descEn = (string) ($description['en'] ?? '');
                    } elseif (is_string($description) && $description !== '') {
                        $descAr = $description;
                        $descEn = $description;
                    }

                    return [
                        'item_id' => $itemId,
                        'album_id' => (string) $albumId,
                        'title' => ['ar' => $titleAr, 'en' => $titleEn],
                        'description' => ['ar' => $descAr, 'en' => $descEn],
                        'media' => [$m->getAttributeValue('uuid') => $m->getAttributeValue('uuid')],
                    ];
                })->values()->all();

                return $album;
            })
            ->values()
            ->all();
    }

    /**
     * Normalize meta coming from the form on save (no media expansion).
     *
     * @param  array<int, mixed>  $albums
     * @return array<int, mixed>
     */
    public static function normalizeIncomingMeta(array $albums): array
    {
        return collect($albums)
            ->filter(fn ($item) => is_array($item))
            ->unique('album_id')
            ->map(function (array $album) {
                $parentAlbumId = (string) ($album['album_id'] ?? '');
                $album['items'] = collect($album['items'] ?? [])
                    ->filter(fn ($item) => is_array($item))
                    ->map(function (array $item) use ($parentAlbumId) {
                        $item['item_id'] = $item['item_id'] ?? Str::random(12);
                        $item['album_id'] = (string) ($item['album_id'] ?? $parentAlbumId);
                        $item['title'] = array_merge(
                            ['ar' => '', 'en' => ''],
                            is_array($item['title'] ?? null) ? $item['title'] : []
                        );
                        $item['description'] = array_merge(
                            ['ar' => '', 'en' => ''],
                            is_array($item['description'] ?? null) ? $item['description'] : []
                        );

                        return $item;
                    })
                    ->unique('item_id')
                    ->values()
                    ->all();

                return $album;
            })
            ->values()
            ->all();
    }

    /**
     * Allowed (album_id, item_id) pairs from normalized meta.
     *
     * @param  array<int, mixed>  $albums
     * @return \Illuminate\Support\Collection<int, array{album_id: string, item_id: string}>
     */
    public static function allowedAlbumItemPairs(array $albums): \Illuminate\Support\Collection
    {
        return collect($albums)
            ->filter(fn ($item) => is_array($item))
            ->flatMap(function (array $album) {
                $albumId = (string) ($album['album_id'] ?? '');

                return collect($album['items'] ?? [])
                    ->filter(fn ($i) => is_array($i))
                    ->map(fn (array $item) => [
                        'album_id' => (string) ($item['album_id'] ?? $albumId),
                        'item_id' => (string) ($item['item_id'] ?? ''),
                    ])
                    ->filter(fn (array $pair) => $pair['album_id'] !== '' && $pair['item_id'] !== '');
            });
    }
}
