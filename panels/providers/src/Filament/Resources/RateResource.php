<?php

namespace App\ProviderPanel\Filament\Resources;

use App\CatalogModule\Models\Reservation\Rate;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ProviderPanel\Filament\Resources\RateResource\Pages\ListRates;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RateResource extends Resource {
    use HasTranslationLabel;

    protected static ?string $model = Rate::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form {

        return $form;

    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query->whereHas('reservation', fn($q) => $q->where("reservable_id", provider()->id)))
            ->columns([
                TextColumn::make('id')->searchable(),

                TextColumn::make('reservation.customer.name'),
                TextColumn::make('reservation.customer.phone'),
                TextColumn::make('type')->formatStateUsing(fn($record) => __("forms.fields.".$record->type."_rate")),
                TextColumn::make('rate'),
                TextColumn::make('comment'),
                TextColumn::make('created_at')->searchable()->date(),


            ])
            ->groups([
                Group::make('reservation_id')
                    ->label(__("forms.fields.reservation_id")),

            ])
            ->defaultGroup('reservation_id')
            ;
    }

    public static function getPages(): array {
        return [
            'index' => ListRates::route('/'),

        ];
    }

    public static function getRelations(): array {
        return [


        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::whereHas('reservation', fn($q) => $q->where("reservable_id", provider()->id))->count();
    }

    public static function can(string $action, ?Model $record = null): bool {
        return true;
    }

}
