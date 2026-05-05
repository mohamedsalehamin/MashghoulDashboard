<?php

namespace App\ContentModule\Resources\ProviderActivityResource\Pages;

use App\ContentModule\Resources\ProviderActivityResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditProviderActivity extends EditRecord
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
