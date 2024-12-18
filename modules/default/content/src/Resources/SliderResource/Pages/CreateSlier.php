<?php

namespace App\ContentModule\Resources\SliderResource\Pages;

use App\ContentModule\Resources\BannerResource;
use App\ContentModule\Resources\SliderResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateSlier extends CreateRecord {
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

