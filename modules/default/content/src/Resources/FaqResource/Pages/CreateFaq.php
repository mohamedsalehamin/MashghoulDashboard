<?php

namespace App\ContentModule\Resources\FaqResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\ContentModule\Resources\FaqResource;

class CreateFaq extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = FaqResource::class;
    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}
