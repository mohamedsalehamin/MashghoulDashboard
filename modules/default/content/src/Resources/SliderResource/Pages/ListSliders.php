<?php

namespace App\ContentModule\Resources\SliderResource\Pages;

use Filament\Actions\CreateAction;
use App\ContentModule\Resources\BannerResource;
use App\ContentModule\Resources\SliderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListSliders extends ListRecords
{
    protected static string $resource = SliderResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
