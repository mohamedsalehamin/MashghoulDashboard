<?php

namespace App\ContentModule\Resources\ProviderActivityResource\Pages;

use App\ContentModule\Resources\ProviderActivityResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateProviderActivity extends CreateRecord
{
    use Translatable;

    protected static string $resource = ProviderActivityResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
