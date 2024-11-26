<?php

namespace App\ContentModule\Resources\PostResource\Pages;

use App\ContentModule\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;
    use CreateRecord\Concerns\Translatable;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug']  = $data['title'];
        return $data;
    }
}
