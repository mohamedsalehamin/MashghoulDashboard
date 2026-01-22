<?php

namespace App\CatalogModule\Resources\ServiceResource\RelationManagers;

use App\CatalogModule\Models\Reservation\ItemsLine;
use App\CatalogModule\Models\Service;
use App\DefaultPanel\Lib\Utils;
use App\Notifications\LabReservationResultsAddedNotification;
use Cknow\Money\Money;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;


class ProductsRelationManager extends RelationManager {
    protected static string $relationship = 'products';

    public function form(Form $form): Form {
        return $form
            ->schema([

            ]);
    }

    public function table(Table $table): Table {
        return $table
            ->heading(__('sections.products'))
            ->recordTitleAttribute('name')
            ->columns([

                Tables\Columns\TextColumn::make('id'),
                SpatieMediaLibraryImageColumn::make('avatar'),

                Tables\Columns\TextColumn::make('title')->formatStateUsing(fn($record)=>$record->title[app()->getLocale()]??''),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('sale_price'),

            ])
            ->filters([
                //
            ])
//            ->headerActions([
//                Tables\Actions\CreateAction::make(),
//            ])
            ->emptyStateHeading(__('panel.messages.no_products'))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // ExportBulkAction::make(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


}
