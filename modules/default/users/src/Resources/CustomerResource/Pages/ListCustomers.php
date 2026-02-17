<?php

namespace App\UsersModule\Resources\CustomerResource\Pages;

use Filament\Actions\CreateAction;
use App\UsersModule\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCustomers extends ListRecords {
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array {
        return [
            CreateAction::make()
        ];
    }

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}
