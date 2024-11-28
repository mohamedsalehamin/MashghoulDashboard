<?php

namespace App\CatalogModule\Resources\CategoryResource\Pages;

use App\CatalogModule\Resources\CategoryResource;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    public function infolist($infolist): \Filament\Infolists\Infolist {
        return  $infolist->schema([
            TextEntry::make('name'),

            SpatieMediaLibraryImageEntry::make('avatar')
                ->label(__('forms.fields.image')),

        ])->columns(1);

    }

}
