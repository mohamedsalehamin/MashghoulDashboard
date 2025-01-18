<?php

namespace App\CatalogModule\Resources\ServiceResource\RelationManagers;

use App\CatalogModule\Models\Reservation\ItemsLine;
use App\CatalogModule\Models\Service;
use App\DefaultPanel\Lib\Utils;
use App\Notifications\LabReservationResultsAddedNotification;
use Cknow\Money\Money;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
                Tables\Columns\TextColumn::make('title')->formatStateUsing(fn($record)=>$record->title[app()->getLocale()]??''),
                Tables\Columns\TextColumn::make('price'),


            ])
            ->filters([
                //
            ])
//            ->headerActions([
//                Tables\Actions\CreateAction::make(),
//            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()
                            ->withFilename(date('Y-m-d') . '-products-export')
                    ]),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


}
