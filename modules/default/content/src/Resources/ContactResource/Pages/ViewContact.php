<?php

namespace App\ContentModule\Resources\ContactResource\Pages;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use App\ContentModule\Resources\ContactResource;
use Filament\Infolists;
use Filament\Resources\Pages\ViewRecord;

class ViewContact extends ViewRecord {
    protected static string $resource = ContactResource::class;

    public function infolist(Schema $schema): Schema {
        return $infolist
            ->schema([
                TextEntry::make('phone'),

            ]);

    }
}
