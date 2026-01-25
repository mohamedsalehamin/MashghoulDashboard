<?php

namespace App\ProviderPanel\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\ProviderPanel\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\ProviderPanel\Filament\Resources\CustomerResource\Pages\ViewCustomer;
use App\ProviderPanel\Filament\Resources\CustomerResource\RelationManagers\ReservationsRelationManager;
use App\UsersModule\Models\Users\Customer;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class CustomerResource extends Resource {
    use HasTranslationLabel;

    protected static ?string $model = Customer::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema {

        return $schema;

    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query->whereHas('reservations', fn($q) => $q->where("reservable_id", provider()->id)))
            ->columns([
                TextColumn::make('id')->searchable(),

                TextColumn::make('data.first_name')
                    ->searchable(true, fn(Builder $query, $search) => $query->where('data->first_name', 'like', '%' . $search . '%')),
                TextColumn::make('data.last_name')
                    ->searchable(true, fn(Builder $query, $search) => $query->where('data->last_name', 'like', '%' . $search . '%')),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('created_at')->searchable()->date(),


            ])
            ->recordActions([
                ViewAction::make()
            ])
            ->toolbarActions([
                ExportBulkAction::make(),
            ])
            ->filters([
                Filter::make('ID')
                    ->schema([TextInput::make('id'),])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['id'], fn(Builder $query, $date): Builder => $query->where('id', $data['id']));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['id']) {
                            return null;
                        }

                        return __('forms.fields.id') . " CustomerResource.php" . $data['id'];
                    }),
                SelectFilter::make('gender')
                    ->searchable()
                    ->options(GenderEnum::class),

                SelectFilter::make('active')
                    ->options(ModelStatus::class)
            ]);
    }

    public static function getPages(): array {
        return [
            'index' => ListCustomers::route('/'),
            'view' => ViewCustomer::route('/{record}'),

        ];
    }
    public static function infolist(Schema $schema): Schema {
        return $schema->components([
            TextEntry::make("id"),
            TextEntry::make("data.first_name")->label(__('forms.fields.first_name')),
            TextEntry::make("data.last_name")->label(__('forms.fields.last_name')),
            TextEntry::make("phone"),
            TextEntry::make("email"),
            TextEntry::make("city.state.country.name")->label(__('forms.fields.country_id')),
            TextEntry::make("city.state.name")->label(__('forms.fields.state')),
            TextEntry::make("city.name")->label(__("forms.fields.city_name")),
            TextEntry::make("created_at")->label(__('forms.fields.created_at')),
            TextEntry::make("active")->label(__("forms.fields.status")),
            TextEntry::make("points")->label(__("forms.fields.points"))->state(fn($record) => $record->getTotalPoints()),

        ]);
    }
    public static function getRelations(): array {
        return [
            ReservationsRelationManager::make()

        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::whereHas('reservations', fn($q) => $q->where("reservable_id", provider()->id))->count();
    }


}
