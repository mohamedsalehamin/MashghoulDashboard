<?php

namespace App\UsersModule\Resources\CustomerResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use App\CatalogModule\Resources\ReservationResource;
use App\DefaultPanel\Enum\ServicesTypeEnum;
use App\UsersModule\Models\Doctor;
use Cknow\Money\Money;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

    public function form(Schema $schema): Schema {
        return $schema
            ->components([

                TextInput::make('name')->required(),
                SpatieMediaLibraryFileUpload::make('image'),

            ])->columns(1);
    }

    public function table(Table $table): Table {
        return $table

            ->heading(__('sections.reservations'))
            ->emptyStateHeading('')
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('reservable.name')->label(__('forms.fields.provider_name'))->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->searchable(),
                TextColumn::make('date')
                    ->date()
                    ->searchable(),
                TextColumn::make('from')->searchable(),
                TextColumn::make('to')->searchable(),
                TextColumn::make('price')
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('forms.fields.status'))
                    ->color(fn($record) => $record?->status?->getColor())
                    ->badge(),
                TextColumn::make('transaction.status')
                    ->formatStateUsing(fn($record) => $record->getPaymentStatus()->getLabel())
                    ->label(__('forms.fields.payment_status'))
                    ->color(fn($record) => $record?->getPaymentStatus()?->getColor())
                    ->badge(),

                TextColumn::make('price'),
                TextColumn::make('meta_data.points')->label(__('forms.fields.points'))->searchable(),

            ])
            ->recordActions([
                ViewAction::make()->url(fn(Model $record) => ReservationResource::getUrl('view', [$record->id])),
            ]);
    }

    protected static function getModelLabel(): ?string {
        return __('sections.reservations');
    }

    protected function can(string $action, ?Model $record = null): bool {
        return true;
    }

}
