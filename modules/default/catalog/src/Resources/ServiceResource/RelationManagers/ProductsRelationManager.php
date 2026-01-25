<?php

namespace App\CatalogModule\Resources\ServiceResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\CatalogModule\Models\Reservation\ItemsLine;
use App\CatalogModule\Models\Service;
use App\DefaultPanel\Lib\Utils;
use App\Notifications\LabReservationResultsAddedNotification;
use Cknow\Money\Money;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;


class ProductsRelationManager extends RelationManager {
    protected static string $relationship = 'products';

    public function form(Schema $schema): Schema {
        return $schema
            ->components([

            ]);
    }

    public function table(Table $table): Table {
        return $table
            ->heading(__('sections.products'))
            ->recordTitleAttribute('name')
            ->columns([

                TextColumn::make('id'),
                SpatieMediaLibraryImageColumn::make('avatar'),

                TextColumn::make('title')->formatStateUsing(fn($record)=>$record->title[app()->getLocale()]??''),
                TextColumn::make('price'),
                TextColumn::make('sale_price'),

            ])
            ->filters([
                //
            ])
//            ->headerActions([
//                Tables\Actions\CreateAction::make(),
//            ])
            ->emptyStateHeading(__('panel.messages.no_products'))
            ->toolbarActions([
                BulkActionGroup::make([
                    // ExportBulkAction::make(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }


}
