<?php

namespace App\ContentModule\Resources\LevelResource\Pages;

use Filament\Actions\CreateAction;
use App\ContentModule\Resources\LevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListLevels extends ListRecords
{
    protected static string $resource = LevelResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array {
        return [
            CreateAction::make(),
//            Actions\LocaleSwitcher::make(),
        ];
    }
}
