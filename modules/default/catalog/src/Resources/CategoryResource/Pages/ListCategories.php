<?php

namespace App\CatalogModule\Resources\CategoryResource\Pages;

use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use App\CatalogModule\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected string $view = 'filament.pages.listing.categories';

    protected function getHeaderActions(): array {
        return [
            CreateAction::make(),
//            Actions\LocaleSwitcher::make(),
        ];
    }


    protected function getTableQuery(): Builder {
        return parent::getTableQuery()->parent();
    }

}
