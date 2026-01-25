<?php

namespace App\CatalogModule\Resources\ServiceResource\Pages;


use Filament\Actions\CreateAction;
use App\CatalogModule\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\CatalogModule\Resources\PlanResource;


class ListServices extends ListRecords {
    protected static string $resource = ServiceResource::class;


    protected function getHeaderActions(): array {
        return [
            CreateAction::make()
        ];
    }

}
