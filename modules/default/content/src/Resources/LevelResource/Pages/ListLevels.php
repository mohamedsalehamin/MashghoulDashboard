<?php

namespace App\ContentModule\Resources\LevelResource\Pages;

use App\ContentModule\Resources\LevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\ContentModule\Resources\PageResource;

class ListLevels extends ListRecords {

    protected static string $resource = LevelResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make(),
//            Actions\LocaleSwitcher::make(),
        ];
    }
}
