<?php

namespace App\ContentModule\Resources\StateResource\Pages;

use Filament\Actions\CreateAction;
use App\ContentModule\Resources\StateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListStates extends ListRecords
{
    protected static string $resource = StateResource::class;

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
