<?php

namespace App\ProviderPanel\Filament\Resources\ReservationResource\RelationManagers;

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
use Illuminate\Support\HtmlString;
use NumberFormatter;

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
                Tables\Columns\TextColumn::make('model.id')
                    ->formatStateUsing(fn( ItemsLine $record) => Service::find($record->model['id'])->title)
                    ->label(__('forms.fields.name')),

                Tables\Columns\TextColumn::make('products')
                    ->state(function ($record) {
                        $text = [];
                        foreach ($record['attributes']['products'] ?? [] as $index => $option) {
                            $option_name = Utils::getTranslatedField($option['title']);
                            $price = Money::parse($option['price']['amount'])->format(style:NumberFormatter::PATTERN_DECIMAL );
                            $qty =__("forms.fields.quantity").":". $option['quantity'];

                            $text[] = "<p>{$option_name}</p> <span class='text-gray-500'> $qty  </span>";
                        }
                        return (new HtmlString($text))->toHtml();

                    })
                    ->html()
                    ->label(__('forms.fields.products')),

                Tables\Columns\TextColumn::make('total')
                    ->label(__("forms.fields.service_price"))
                    ->state(function (ItemsLine $record) {
                        $price = Money::parse($record->model['price']['amount'] ?? 0)->formatByDecimal();

                        return Money::parse(($record->quantity * $price) + ($record->quantity * collect($record->conditions)->sum('value')))->format();
                    }),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


}
