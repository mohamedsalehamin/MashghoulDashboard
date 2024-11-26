<?php

namespace App\ProviderPanel\Filament\Resources\ReservationResource\RelationManagers;

use App\CatalogModule\Models\Reservation\ItemsLine;
use App\Notifications\LabReservationResultsAddedNotification;
use Cknow\Money\Money;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsLineRelationManager extends RelationManager {
    protected static string $relationship = 'itemsLine';

    public function form(Form $form): Form {
        return $form
            ->schema([

            ]);
    }

    public function table(Table $table): Table {
        return $table
            ->heading(__('sections.services'))
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('model.name.' . app()->getLocale())
                    ->label(__('forms.fields.name')),

                Tables\Columns\TextColumn::make('model.id')

                    ->formatStateUsing(fn($record) => __('forms.fields.view_file'))
                    ->url(fn($record) => $record->getFirstMediaUrl())
                    ->openUrlInNewTab()
                    ->label(__('forms.fields.file')),

                Tables\Columns\TextColumn::make('total')
                    ->state(function (ItemsLine $record) {
                        return Money::parse(($record->quantity * $record->price) + ($record->quantity * collect($record->conditions)->sum('value')))->format();
                    }),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('upload_analysis')
                    ->visible(fn(ItemsLine $record) => !$record->getFirstMediaUrl())
                    ->label(__('forms.actions.upload_analysis'))
                    ->icon('heroicon-o-arrow-up-on-square')
                    ->action(fn($record)=>$record->reservation->patient->notify(new LabReservationResultsAddedNotification($record->reservation)))
                    ->form([
                        SpatieMediaLibraryFileUpload::make('file')
                            ->columnSpan(2),
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


}
