<?php

namespace App\ContentModule\Resources\BannerResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use App\ContentModule\Resources\BannerResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditBanner extends EditRecord
{
    use Translatable;

    protected static string $resource = BannerResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array {
        return [
//            Actions\LocaleSwitcher::make(),

        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
