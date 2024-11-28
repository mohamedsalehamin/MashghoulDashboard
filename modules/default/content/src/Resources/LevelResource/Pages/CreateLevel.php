<?php

namespace App\ContentModule\Resources\LevelResource\Pages;

use App\ContentModule\Resources\LevelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLevel extends CreateRecord
{
    protected static string $resource = LevelResource::class;
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
