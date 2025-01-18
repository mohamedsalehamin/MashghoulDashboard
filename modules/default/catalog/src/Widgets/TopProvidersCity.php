<?php

namespace App\CatalogModule\Widgets;

use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Service;
use App\ContentModule\Models\City;
use App\UsersModule\Models\Provider;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;


class TopProvidersCity extends BaseWidget {
    use HasWidgetShield;
    protected static ?int $sort = 7;
    public function table(Table $table): Table {
        return $table
            ->heading(__('sections.providers_by_city'))
            ->query(fn()=>City::withCount('providers')->orderBy("providers_count", 'desc')->limit(20),)
            ->columns([
                TextColumn::make('index')->rowIndex(),
                TextColumn::make('name')->label(__("forms.fields.city_name")),

                TextColumn::make('providers_count')->label(__("panel.stats.providers_count")),
            ]);
    }


}
