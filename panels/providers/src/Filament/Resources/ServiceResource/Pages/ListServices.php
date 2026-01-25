<?php

namespace App\ProviderPanel\Filament\Resources\ServiceResource\Pages;
use Filament\Actions\CreateAction;
use App\ProviderPanel\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;



class ListServices extends ListRecords {
    protected static string $resource = ServiceResource::class;


    protected function getHeaderActions(): array {
        return [
            CreateAction::make()
        ];
    }

}
