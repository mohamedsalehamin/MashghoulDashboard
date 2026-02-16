<?php

namespace App\ContentModule\Resources\SliderResource\Pages;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use App\ContentModule\Resources\BannerResource;
use App\ContentModule\Resources\SliderResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditSlider extends EditRecord
{
    use Translatable;

    protected static string $resource = SliderResource::class;

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
