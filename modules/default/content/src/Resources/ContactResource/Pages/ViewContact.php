<?php

namespace App\ContentModule\Resources\ContactResource\Pages;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use App\ContentModule\Resources\ContactResource;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    public function infolist(Schema $schema): Schema {
        return $infolist
            ->schema([
                TextEntry::make('phone'),

            ]);

    }
}
