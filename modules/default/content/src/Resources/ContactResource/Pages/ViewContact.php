<?php

namespace App\ContentModule\Resources\ContactResource\Pages;

use App\ContentModule\Resources\ContactResource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewContact extends ViewRecord {
    protected static string $resource = ContactResource::class;

    public function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('phone'),

            ]);

    }
}
