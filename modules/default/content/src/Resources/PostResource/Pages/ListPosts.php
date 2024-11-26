<?php

namespace App\ContentModule\Resources\PostResource\Pages;

use App\ContentModule\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords {

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make(),
//            Actions\LocaleSwitcher::make(),
        ];
    }
}
