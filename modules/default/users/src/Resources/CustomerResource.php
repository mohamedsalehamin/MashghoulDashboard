<?php

namespace App\UsersModule\Resources;

use App\CatalogModule\Models\Reservation;
use App\ContentModule\Models\City;
use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Filters\DateFilter;
use App\DefaultPanel\Filters\LocationFilter;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\UsersModule\Models\Users\Customer;
use App\UsersModule\Resources\CustomerResource\Pages\CreateCustomer;
use App\UsersModule\Resources\CustomerResource\Pages\EditCustomer;
use App\UsersModule\Resources\CustomerResource\Pages\ListCustomers;
use App\UsersModule\Resources\CustomerResource\Pages\ViewCustomer;
use App\UsersModule\Resources\CustomerResource\RelationManagers\ReservationsRelationManager;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use libphonenumber\PhoneNumberType;
use Money\Money;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CustomerResource extends Resource {
    use HasTranslationLabel;

    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form {

        return $form->schema([
            Group::make([
                Section::make("basic_information")->schema(array(
                    SpatieMediaLibraryFileUpload::make('avatar')
                        ->nullable()
                        ->columnSpan(2),

                    TextInput::make('data.first_name')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($set, $get) => $set('name', $get('data.first_name') . ' ' . $get('data.last_name')))
                        ->required()
                        ->columnSpan(1)
                        ->minLength(3),

                    TextInput::make('data.last_name')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($set, $get) => $set('name', $get('data.first_name') . ' ' . $get('data.last_name')))
                        ->minLength(3)
                        ->columnSpan(1)
                        ->required(),

                    Hidden::make('name'),

                    PhoneInput::make('phone')
                        ->required()
                        ->onlyCountries(array('SA', 'EG'))
                        ->validateFor(
                            type: PhoneNumberType::MOBILE,
                            lenient: true
                        )
                        ->unique(ignoreRecord: true)
                        ->displayNumberFormat(PhoneInputNumberType::E164),


                    TextInput::make('email')
//                        ->required()
                        ->email()
//                        ->unique(ignoreRecord: true)
                        ->autocomplete("off"),

                    Hidden::make('password')->default('password'),

                    Select::make('gender')
                        ->required()
                        ->options(array(
                            'male' => __("panel.enums.male"),
                            'female' => __("panel.enums.female"),
                        )),
                    DatePicker::make('dob')
                        ->before(now()->toDateString()),
                    Select::make('country_id')
                        ->live()
                        ->required()
                        ->formatStateUsing(fn($record) => $record?->city?->state->country_id)
                        ->options(function ($get, $set, $record) {

                            return Country::pluck('name', 'id');
                        }),


                    Select::make('state_id')
                        ->live()
                        ->required()
                        ->formatStateUsing(fn($record) => $record?->city?->state_id)
                        ->options(function ($get, $set, $record) {

                            return State::where('country_id', $get('country_id'))->pluck('name', 'id');
                        }),

                    Select::make('city_id')
                        ->label(__('forms.fields.city_id'))
                        ->options(fn($get) => City::where('state_id', $get('state_id'))->pluck('name', 'id')),
                ))
                    ->columns(2)
            ])
                ->columnSpan(2),

        ])->columns(1);

    }

    public static function table(Table $table): Table {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->searchable(),

                TextColumn::make('data.first_name')
                    ->searchable(true, fn(Builder $query, $search) => $query->where('data->first_name', 'like', '%' . $search . '%')),
                TextColumn::make('data.last_name')
                    ->searchable(true, fn(Builder $query, $search) => $query->where('data->last_name', 'like', '%' . $search . '%')),
                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('city.state.country.name')
                    ->label(__('forms.fields.country_id'))
                    ->searchable(),
                TextColumn::make('city.state.name')
                    ->label(__('forms.fields.state'))
                    ->searchable(),
                TextColumn::make('city.name')
                    ->label(__("forms.fields.city_name"))
                    ->searchable(),
                TextColumn::make('completed_reservations_count')
                    ->counts('completedReservations')
                    ->sortable()
                    ->label(__('forms.fields.reservations_count'))
                    ->searchable(),
                TextColumn::make('completed_reservations_sum_price')
                    ->formatStateUsing(fn($state) => \Cknow\Money\Money::parse($state ?? 0, 'SAR')->format())
                    ->sum('completedReservations', 'price')
                    ->sortable()
                    ->label(__('forms.fields.reservations_totals'))
                    ->searchable(),
                TextColumn::make('created_at')->searchable()->date(),
                TextColumn::make('active')
                    ->label(__('forms.fields.status'))
                    ->formatStateUsing(fn($record) => $record->active->getLabel())
                    ->color(fn($record) => $record->active->getColor())
                    ->action(
                        Action::make('Active')
                            ->label(fn(Model $record): string => $record->active ? __('panel.messages.deactivate') : __('panel.messages.activate'))
//                            ->disabled(fn(Model $record): bool => !auth()->user()->can('update', static::getModel()))
                            ->requiresConfirmation()
                            ->action(fn(Model $record) => $record->toggleActive())


                    )
                    ->badge(),


            ])
            ->filters([
                Filter::make('ID')
                    ->form([TextInput::make('id'),])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['id'], fn(Builder $query, $date): Builder => $query->where('id', $data['id']));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['id']) {
                            return null;
                        }

                        return __('forms.fields.id') . " " . $data['id'];
                    }),
                LocationFilter::make(),
                SelectFilter::make('gender')
                    ->searchable()
                    ->options(GenderEnum::class),

                SelectFilter::make('active')
                    ->options(ModelStatus::class),
                DateFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),

                ]),
            ]);
    }

    public static function getPages(): array {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
            'view' => ViewCustomer::route('/{record}'),
        ];
    }

    public static function getRelations(): array {
        return [
            ReservationsRelationManager::make()

        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.crew');
    }

    public static function infolist(Infolist $infolist): Infolist {
        return $infolist->schema([
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
}
