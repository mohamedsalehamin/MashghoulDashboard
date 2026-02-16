<?php

namespace App\ContentModule\Resources\FaqResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use App\ContentModule\Resources\FaqResource;

class ListFaq extends ListRecords
{
    protected static string $resource = FaqResource::class;

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
