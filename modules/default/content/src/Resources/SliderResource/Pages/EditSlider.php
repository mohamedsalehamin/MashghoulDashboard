<?php

namespace App\ContentModule\Resources\SliderResource\Pages;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use App\ContentModule\Resources\BannerResource;
use App\ContentModule\Resources\SliderResource;
use Filament\Resources\Pages\EditRecord;

class EditSlider extends EditRecord {
    use Translatable;

    protected static string $resource = SliderResource::class;


    protected function getHeaderActions(): array {
        return [
//            Actions\LocaleSwitcher::make(),

        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
