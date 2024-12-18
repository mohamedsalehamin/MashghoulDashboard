<?php

namespace App\ContentModule\Resources\SliderResource\Pages;

use App\ContentModule\Resources\BannerResource;
use App\ContentModule\Resources\SliderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSliders extends ListRecords
{
    protected static string $resource = SliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
