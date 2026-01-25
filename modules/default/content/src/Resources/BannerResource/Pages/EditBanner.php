<?php

namespace App\ContentModule\Resources\BannerResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use App\ContentModule\Resources\BannerResource;
use Filament\Resources\Pages\EditRecord;

class EditBanner extends EditRecord {
    use Translatable;

    protected static string $resource = BannerResource::class;


    protected function getHeaderActions(): array {
        return [
//            Actions\LocaleSwitcher::make(),

        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
