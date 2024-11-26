<?php

namespace App\ContentModule\Resources\StateResource\Pages;

use App\ContentModule\Resources\StateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditState extends EditRecord {
    use EditRecord\Concerns\Translatable;

    protected static string $resource = StateResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
