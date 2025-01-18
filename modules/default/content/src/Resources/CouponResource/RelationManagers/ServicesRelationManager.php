<?php

namespace App\ContentModule\Resources\CouponResource\RelationManagers;

use App\CatalogModule\Models\Product;
use App\CatalogModule\Models\Service;
use App\UsersModule\Models\Provider;
use App\UsersModule\Resources\CustomerResource;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesRelationManager extends RelationManager {
    protected static string $relationship = 'services';

    public function form(Form $form): Form {
        return $form
            ->schema([

                Select::make('provider_id')
                    ->label(__("forms.fields.provider_name"))
                    ->searchable()
                    ->live()
                    ->options(Provider::pluck("name", "id")),

                Select::make('service_id')
                    ->live()
                    ->label(__("forms.fields.service_name"))
                    ->searchable()
                    ->options(fn($get) => Service::where('provider_id', $get('provider_id'))->pluck("title", "id")),
                CheckboxList::make('products')
                    ->getOptionLabelFromRecordUsing(fn($record) =>  $record->title[app()->getLocale()]??'')
                    ->relationship('products','title',fn($query,$get)=>$query->where('service_id', $get('service_id')))
            ])->columns(1);
    }

    public function table(Table $table): Table {
        return $table
            ->heading(__('sections.providers'))
            ->modelLabel(__("menu.provider"))
            ->columns([
                TextColumn::make('id')->searchable()->toggleable(false),
                TextColumn::make('provider.name')->label(__('forms.fields.provider_name'))->searchable()->toggleable(false),
                TextColumn::make('service.title')->label(__('forms.fields.service_name'))->searchable()->toggleable(false),
                TextColumn::make('products.title')
                    ->formatStateUsing(fn($record) =>$record->products->pluck("title.".app()->getLocale())->implode(', '))
                    ->label(__('forms.fields.products'))->searchable()->toggleable(false),

            ])
            ->emptyStateHeading(__('site.no_data'))
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->bulkActions([
            ]);
    }


}
