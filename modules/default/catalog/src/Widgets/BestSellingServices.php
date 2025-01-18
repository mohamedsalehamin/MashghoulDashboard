<?php

namespace App\CatalogModule\Widgets;

use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Service;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;


class BestSellingServices extends BaseWidget {
    use HasWidgetShield;
    protected static ?int $sort = 7;
    public function table(Table $table): Table {
        return $table
            ->heading(__('sections.best_selling_products'))
            ->description(__('sections.best_selling_products_description'))
            ->query(
                Service::whereHas('paidReservations')
                    ->withCount('paidReservations')->orderBy("paid_reservations_count", 'desc')
                    ->limit(5),
            )
            ->columns([
                TextColumn::make('index')->rowIndex(),
                SpatieMediaLibraryImageColumn::make('image'),

                TextColumn::make('title'),
                TextColumn::make('paid_reservations_count')
                ->label(__("panel.stats.reservations_count")),
            ]);
    }

     public function getTableHeading(): ?string {
        return __('sections.best_selling_products');
    }
}
