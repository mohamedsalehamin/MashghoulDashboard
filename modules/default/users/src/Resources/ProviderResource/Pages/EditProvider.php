<?php

namespace App\UsersModule\Resources\ProviderResource\Pages;

use App\UsersModule\Resources\ProviderResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditProvider extends EditRecord
{
    protected static string $resource = ProviderResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    /**
     * Keep one repeater row per album_id (fixes duplicates from legacy array_replace_recursive merges).
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->normalizePortfolioAlbumsInFormData($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->normalizePortfolioAlbumsInFormData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizePortfolioAlbumsInFormData(array $data): array
    {
        $albums = data_get($data, 'provider.meta_data.portfolio_albums');
        if (! is_array($albums)) {
            return $data;
        }
        $normalized = collect($albums)
            ->filter(fn ($item) => is_array($item))
            ->unique('album_id')
            ->values()
            ->all();
        data_set($data, 'provider.meta_data.portfolio_albums', $normalized);

        return $data;
    }

    protected function afterSave(): void
    {
        $provider = $this->record->provider;
        if (! $provider) {
            return;
        }

        $allowedAlbumIds = collect(data_get($provider->meta_data, 'portfolio_albums', []))
            ->pluck('album_id')
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->unique();

        foreach ($provider->getMedia('portfolio') as $mediaItem) {
            $albumId = $mediaItem->getCustomProperty('album_id');
            if ($albumId !== null && $albumId !== '' && ! $allowedAlbumIds->contains($albumId)) {
                $mediaItem->delete();
            }
        }
    }
}
