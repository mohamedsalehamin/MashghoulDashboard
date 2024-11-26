<?php

namespace App\ContentModule\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\ContentModule\Resources\PageResource;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
    use CreateRecord\Concerns\Translatable;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
