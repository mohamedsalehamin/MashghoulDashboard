<?php

namespace App\CatalogModule\Resources\SpecializationResource\Pages;

use App\CatalogModule\Resources\CategoryResource;
use App\CatalogModule\Resources\SpecializationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpecialties extends ListRecords {

    protected static string $resource = SpecializationResource::class;


    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make(),
//            Actions\LocaleSwitcher::make(),
        ];
    }


    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder {
        return parent::getTableQuery()->parent();
    }

}
