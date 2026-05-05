<?php

namespace App\ContentModule\Resources\ProviderActivityResource\Pages;

use App\ContentModule\Resources\ProviderActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListProviderActivities extends ListRecords
{
    protected static string $resource = ProviderActivityResource::class;

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
