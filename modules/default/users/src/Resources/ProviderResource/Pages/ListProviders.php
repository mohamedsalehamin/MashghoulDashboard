<?php

namespace App\UsersModule\Resources\ProviderResource\Pages;

use Filament\Actions\CreateAction;
use App\UsersModule\Resources\ProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProviders extends ListRecords {
    protected static string $resource = ProviderResource::class;

    protected function getHeaderActions(): array {
        return [
            CreateAction::make()
        ];
    }
}
