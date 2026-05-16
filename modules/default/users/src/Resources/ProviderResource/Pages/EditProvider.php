<?php

namespace App\UsersModule\Resources\ProviderResource\Pages;

use App\Support\PortfolioAlbumsFormState;
use App\UsersModule\Models\Provider as SalonProvider;
use App\UsersModule\Resources\ProviderResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        $albums = data_get($data, 'provider.meta_data.portfolio_albums');
        if (is_array($albums)) {
            $provider = $this->record?->provider;
            data_set($data, 'provider.meta_data.portfolio_albums', PortfolioAlbumsFormState::normalizeAlbums($provider, $albums));
        }

        if (empty(data_get($data, 'provider.slug')) && ($provider = $this->record?->provider)) {
            data_set($data, 'provider.slug', SalonProvider::generateUniqueSlug($provider, $provider->id));
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $salon = $this->record?->provider;
        if ($salon) {
            $slug = Str::slug(trim((string) data_get($data, 'provider.slug', '')), '-', 'en');
            if ($slug === '') {
                $slug = SalonProvider::generateUniqueSlug($salon, $salon->id);
            }
            Validator::make(
                ['slug' => $slug],
                [
                    'slug' => [
                        'required',
                        'max:255',
                        Rule::unique('providers', 'slug')->ignore($salon->id),
                    ],
                ],
                [],
                ['slug' => __('forms.fields.slug')]
            )->validate();
            data_set($data, 'provider.slug', $slug);
        }

        $albums = data_get($data, 'provider.meta_data.portfolio_albums');
        if (is_array($albums)) {
            data_set($data, 'provider.meta_data.portfolio_albums', PortfolioAlbumsFormState::normalizeIncomingMeta($albums));
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $provider = $this->record->provider;
        if (! $provider) {
            return;
        }

        $pairs = PortfolioAlbumsFormState::allowedAlbumItemPairs(
            data_get($provider->meta_data, 'portfolio_albums', [])
        );
        $allowedAlbumIds = $pairs->pluck('album_id')->unique();

        foreach ($provider->getMedia('portfolio') as $mediaItem) {
            $albumId = $mediaItem->getCustomProperty('album_id');
            $itemId = (string) ($mediaItem->getCustomProperty('item_id') ?? '');
            if ($albumId !== null && $albumId !== '' && ! $allowedAlbumIds->contains($albumId)) {
                $mediaItem->delete();

                continue;
            }
            if ($itemId === '') {
                continue;
            }
            if (! $pairs->contains(fn (array $p) => $p['album_id'] === (string) $albumId && $p['item_id'] === $itemId)) {
                $mediaItem->delete();
            }
        }
    }
}
