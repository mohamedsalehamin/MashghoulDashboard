<?php

namespace App\ContentModule\Resources\FaqResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\ContentModule\Resources\FaqResource;

class ListFaq extends ListRecords
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
