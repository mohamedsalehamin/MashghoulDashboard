<?php

namespace App\UsersModule\Resources\CustomerResource\RelationManagers;

use App\DefaultPanel\Enum\ServicesTypeEnum;
use App\UsersModule\Models\Doctor;
use Cknow\Money\Money;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\CatalogModule\Models\Reservation\ItemsLine;
use App\DefaultPanel\Lib\Utils;
use Illuminate\Database\Eloquent\Model;

class ReservationsRelationManager extends RelationManager {
    protected static string $relationship = 'reservations';
    protected static bool $shouldSkipAuthorization = true;

    public function form(Form $form): Form {
        return $form
            ->schema([

                TextInput::make('name')->required(),
                SpatieMediaLibraryFileUpload::make('image'),

            ])->columns(1);
    }

    public function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('reservable_type', Doctor::class))
            ->heading(__('sections.reservations'))
            ->columns([
                TextColumn::make('id')->searchable(),

                TextColumn::make('date')
                    ->date()
                    ->searchable(),
                TextColumn::make('period')->searchable(),
                TextColumn::make('price')
                    ->searchable(),
                TextColumn::make('reserve_type')
                    ->color(fn($record) => $record->reserve_type->getColor())
                    ->badge(),

                TextColumn::make('service_type')
                    ->color(fn($record) => $record->service_type->getColor())
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('forms.fields.status'))
                    ->color(fn($record) => $record?->status?->getColor())
                    ->badge(),
                TextColumn::make('transaction.status')
                    ->label(__('forms.fields.payment_status'))
                    ->color(fn($record) => $record?->transaction?->status?->getColor())
                    ->badge(),

                TextColumn::make('price'),

            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn(Model $record) => route('filament.doctor-panel.resources.reservations.view', $record->id)),
            ]);
    }

    protected static function getModelLabel(): ?string {
        return __('sections.reservations');
    }

    protected function can(string $action, ?Model $record = null): bool {
        return true;
    }

}
