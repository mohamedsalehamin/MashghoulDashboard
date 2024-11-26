<?php

namespace App\ContentModule\Resources\FaqResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\ContentModule\Resources\FaqResource;

class EditFaq extends EditRecord
{
    use EditRecord\Concerns\Translatable;

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
