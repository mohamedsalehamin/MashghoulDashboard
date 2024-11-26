<?php

namespace App\ContentModule\Resources\StateResource\Pages;

use App\ContentModule\Resources\StateResource;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;

class CreateState extends CreateRecord {
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = StateResource::class;

    protected function getHeaderActions(): array {
        return [
           LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
