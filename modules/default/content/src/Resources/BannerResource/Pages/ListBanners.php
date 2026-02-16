<?php

namespace App\ContentModule\Resources\BannerResource\Pages;

use Filament\Actions\CreateAction;
use App\ContentModule\Resources\BannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListBanners extends ListRecords
{
    protected static string $resource = BannerResource::class;

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
