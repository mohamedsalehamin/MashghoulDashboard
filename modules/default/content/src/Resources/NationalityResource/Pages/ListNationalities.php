<?php

namespace App\ContentModule\Resources\NationalityResource\Pages;

use App\ContentModule\Resources\LanguageResource;
use App\ContentModule\Resources\NationalityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNationalities extends ListRecords
{
    protected static string $resource = NationalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
