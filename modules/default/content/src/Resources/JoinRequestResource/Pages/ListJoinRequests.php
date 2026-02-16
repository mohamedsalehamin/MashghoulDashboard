<?php

namespace App\ContentModule\Resources\JoinRequestResource\Pages;

use App\ContentModule\Resources\JoinRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListJoinRequests extends ListRecords
{
    protected static string $resource = JoinRequestResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
