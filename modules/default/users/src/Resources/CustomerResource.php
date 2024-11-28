<?php

namespace App\UsersModule\Resources;

use App\ContentModule\Models\City;
use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\UsersModule\Models\Users\Customer;
use App\UsersModule\Resources\CustomerResource\Pages\CreateCustomer;
use App\UsersModule\Resources\CustomerResource\Pages\EditCustomer;
use App\UsersModule\Resources\CustomerResource\Pages\ListCustomers;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use libphonenumber\PhoneNumberType;
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
                        ->required()
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->autocomplete("off"),

                    TextInput::make('password')
                        ->password()
                        ->required(fn(string $operation): bool => $operation === 'create')
                        ->confirmed()
                        ->autocomplete("new-password"),

                    TextInput::make('password_confirmation')
                        ->password()
                        ->required(fn(string $operation): bool => $operation === 'create')
                        ->autocomplete("off"),

                    Select::make('gender')
                        ->options(array(
                            'male' => __("panel.enums.male"),
                            'female' => __("panel.enums.female"),
                        )),
                    DatePicker::make('dob'),
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
            ->columns([
                TextColumn::make('id')->searchable(),

                TextColumn::make('data.first_name')
                    ->searchable("data->first_name"),
                TextColumn::make('data.last_name')
                    ->searchable("data->last_name"),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('city.state.country.name')
                    ->label(__('forms.fields.country_id'))
                    ->searchable(),
                TextColumn::make('city.state.name')
                    ->label(__('forms.fields.state'))
                    ->searchable(),
                TextColumn::make('city.name')
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
                SelectFilter::make('city_id')
                    ->searchable()
                    ->query(fn(Builder $query, $data) => $query->when($data['value'], fn($query) => $query->where('city_id', $data['value'])))
                    ->options(fn(HasTable $livewire) => City::pluck('name', 'id')),
                SelectFilter::make('gender')
                    ->searchable()
                    ->options(GenderEnum::class),

                SelectFilter::make('active')
                    ->options(ModelStatus::class)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array {
        return [


        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.crew');
    }
}
