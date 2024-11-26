<?php

namespace App\CatalogModule\Resources\SpecializationResource\Pages;

use App\CatalogModule\Resources\SpecializationResource;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewSpecialization extends ViewRecord
{
    protected static string $resource = SpecializationResource::class;

    public function infolist($infolist): \Filament\Infolists\Infolist {
        return  $infolist->schema([
            TextEntry::make('name'),

            SpatieMediaLibraryImageEntry::make('avatar')
                ->label(__('forms.fields.image')),

        ])->columns(1);

    }

}
