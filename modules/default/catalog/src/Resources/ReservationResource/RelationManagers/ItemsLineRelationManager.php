<?php

namespace App\CatalogModule\Resources\ReservationResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\CatalogModule\Models\Reservation\ItemsLine;
use App\CatalogModule\Models\Service;
use App\DefaultPanel\Lib\Utils;
use App\Notifications\LabReservationResultsAddedNotification;
use Cknow\Money\Money;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use NumberFormatter;


class ItemsLineRelationManager extends RelationManager {
    protected static string $relationship = 'itemsLine';

    public function form(Schema $schema): Schema {
        return $schema
            ->components([

            ]);
    }

    public function table(Table $table): Table {
        return $table
            ->heading(__('sections.services'))
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('model.id')
                    ->formatStateUsing(fn( ItemsLine $record) => Service::withoutGlobalScopes()->where("id",$record->model['id'])?->first()->title)
                    ->label(__('forms.fields.name')),

                TextColumn::make('products')
                    ->state(function ($record) {
                        $text = [];
                        foreach ($record['attributes']['products'] ?? [] as $index => $option) {
                            $option_name = Utils::getTranslatedField($option['title']);
                            $price = Money::parse($option['price']['amount'])->format(style:NumberFormatter::PATTERN_DECIMAL );
                            $sale_price = Money::parse($option['sale_price']['amount'])->format();
                            $price_label = __("forms.fields.price") . " : " . $price;
                            $sale_price_label = $option['sale_price']['amount'] > 0 ? "<br/><span class='text-gray-500'>" . __("forms.fields.sale_price") . " : " . $sale_price . "</span>" : '';
                            $qty = __("forms.fields.quantity") . " : " . $option['quantity'];

                            $line = $option['sale_price']['amount'] > 0 ? 'cm-strikethrough' : '';
                            $total = Money::parse(($option['sale_price']['amount'] > 0 ? $option['sale_price']['amount'] : $option['price']['amount']) * $option['quantity'])->format();

                            $total_label = "<br/><span class='text-gray-500'>" . __("forms.fields.products_total") . " : " . $total . "</span>";
                            $text[] = "<p>{$option_name}</p> <span class='text-gray-500'> $qty  </span> <br/><span class='text-gray-500 $line'> $price_label  </span> $sale_price_label $total_label";
                        }
                        return (new HtmlString($text))->toHtml();

                    })
                    ->html()
                    ->label(__('forms.fields.products')),

                TextColumn::make('service_price')
                    ->label(__("forms.fields.service_price"))
                    ->state(function (ItemsLine $record) {
                        $price = Money::parse($record->model['price']['amount'] ?? 0)->formatByDecimal();

                        return Money::parse(($record->quantity * $price) + ($record->quantity * collect($record->conditions)->sum('value')))->format();
                    }),
                TextColumn::make('service_sale_price')
                    ->label(__("forms.fields.service_sale_price"))
                    ->state(function (ItemsLine $record) {

                        $price = Money::parse($record->model['sale_price']['amount'] ?? 0)->formatByDecimal();

                        return Money::parse(($record->quantity * $price) + ($record->quantity * collect($record->conditions)->sum('value')))->format();
                    }),


            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }


}
