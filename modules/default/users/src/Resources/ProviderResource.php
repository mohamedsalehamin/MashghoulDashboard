<?php

namespace App\UsersModule\Resources;

use App\ContentModule\Models\Category;
use App\ContentModule\Models\City;
use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
use App\DefaultPanel\Enum\GenderEnum;
use App\DefaultPanel\Enum\ModelStatus;
use App\DefaultPanel\Filters\DateFilter;
use App\DefaultPanel\Filters\LocationFilter;
use App\DefaultPanel\Filters\ProviderLocationFilter;
use App\DefaultPanel\Settings\GeneralSettings;
use App\DefaultPanel\Traits\Filament\HasTranslationLabel;
use App\UsersModule\Models\Users\Provider;
use App\UsersModule\Resources\ProviderResource\Pages\CreateProvider;
use App\UsersModule\Resources\ProviderResource\Pages\EditProvider;
use App\UsersModule\Resources\ProviderResource\Pages\ListProviders;
use App\UsersModule\Resources\ProviderResource\Pages\WalletPage;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
use MatanYadaev\EloquentSpatial\Objects\Point;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class ProviderResource extends Resource {
    use HasTranslationLabel;

    protected static ?string $model = Provider::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form {

        return $form->schema([
            Tabs::make('')->schema([
                Tabs\Tab::make(__("sections.basic_information"))->schema([
                    SpatieMediaLibraryFileUpload::make('avatar')
                        ->nullable(),

                    TextInput::make('data.first_name')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($set, $get) => $set('name', $get('data.first_name') . ' ' . $get('data.last_name')))
                        ->required()
                        ->minLength(3),

                    TextInput::make('data.last_name')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($set, $get) => $set('name', $get('data.first_name') . ' ' . $get('data.last_name')))
                        ->minLength(3)
                        ->required(),

                    Hidden::make('name'),

                    PhoneInput::make('phone')
                        ->required()
                        ->onlyCountries(['SA', 'EG'])
                        ->validateFor(
                            type: PhoneNumberType::MOBILE,
                            lenient: true
                        )
                        ->unique(ignoreRecord: true)
                        ->displayNumberFormat(PhoneInputNumberType::E164),


                    TextInput::make('email')
//                        ->required()
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
                        ->required()
                        ->options([
                            'male' => __("panel.enums.male"),
                            'female' => __("panel.enums.female"),
                        ]),


                ]),
                Tabs\Tab::make(__("sections.provider_information"))->schema([
                    Group::make()->schema([
                        Tabs::make('Label')
                            ->tabs([
                                Tabs\Tab::make(__('panel.languages.arabic'))
                                    ->schema([
                                        TextInput::make('name.ar')
                                            ->label(__('forms.fields.provider_name'))
                                            ->required(),
                                        Textarea::make('bio.ar')
                                            ->label(__('forms.fields.bio'))
                                            ->required(),
                                    ]),
                                Tabs\Tab::make(__('panel.languages.english'))
                                    ->schema([
                                        TextInput::make('name.en')
                                            ->label(__('forms.fields.provider_name'))
                                            ->required(),
                                        Textarea::make('bio.en')
                                            ->label(__('forms.fields.bio'))
                                            ->required(),
                                    ]),
                            ]),

                        SpatieMediaLibraryFileUpload::make('image')
                            ->nullable(),
                        SpatieMediaLibraryFileUpload::make('images')
                            ->multiple()
                            ->collection("images")
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('commercial_register')
                            ->collection("commercial_register")
                            ->nullable(),

                        Select::make('category_id')
                            ->required()
                            ->options(function ($get, $set, $record) {

                                return Category::pluck('name', 'id');
                            }),
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
                        Map::make('location')
                            ->live()
                            ->geolocate()
                            ->geolocateLabel(__('panel.messages.locate_my_location'))
                            ->formatStateUsing(function ($record) {
                                if (!$record || !$record?->location) return;
                                return [
                                    'lat' => Point::fromWkt($record->location)->latitude,
                                    'lng' => Point::fromWkt($record->location)->longitude,
                                ];
                            })
                            ->autocomplete('address_name')
                            ->debug()
                            ->drawingField("boundaries")
                            ->defaultLocation([24.7136, 46.6753])
                            ->draggable()
                            ->clickable(),
                        Section::make("working_times")
                            ->schema(GeneralSettings::daysListSchema())
                            ->statePath('meta_data.days_list'),

                    ])
                        ->relationship('provider')

                ]),
                Tabs\Tab::make(__("sections.bank_account_information"))->schema([
                    Group::make()->schema([
                        TextInput::make('bank_name'),
                        TextInput::make('account_name'),
                        TextInput::make('account_number'),
                        TextInput::make('iban'),
                    ])->relationship('bankAccount'),

                ]),
                Tabs\Tab::make(__("sections.settings"))->schema([
                    Group::make()->schema([
                        Tabs::make('tabs')
                            ->schema([
                                Tabs\Tab::make(__("panel.languages.arabic"))->schema([
                                    Textarea::make('ar.text_when_order_completed')
                                ]),
                                Tabs\Tab::make(__("panel.languages.english"))->schema([
                                    Textarea::make('en.text_when_order_completed')
                                ])
                            ])->statePath('texts'),



                        Select::make('reservation_flow')->options([
                            'total' => __("panel.messages.pay_reservation_totals"),
                            'fees' => __("panel.messages.pay_reservation_fees")
                        ]),
                    ])
                        ->relationship('options')


                ])
            ])
        ])->columns(1);

    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(fn($query) => $query->whereHas('provider'))
            ->columns([
                TextColumn::make('provider.id')
                    ->default('')
                    ->searchable(),

                TextColumn::make('name')->label(__('forms.fields.provider_account_name'))->searchable(),
                TextColumn::make('provider.name')->label(__('forms.fields.provider_name')),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('provider.city.state.country.name')
                    ->label(__('forms.fields.country_id'))
                    ->searchable(),
                TextColumn::make('provider.city.state.name')
                    ->label(__('forms.fields.state'))
                    ->searchable(),
                TextColumn::make('provider.city.name')
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
                ProviderLocationFilter::make(),
                SelectFilter::make('gender')
                    ->searchable()
                    ->options(GenderEnum::class),

                SelectFilter::make('active')
                    ->options(ModelStatus::class),
                DateFilter::make()
            ])
            ->actions([
                Action::make("wallet")
                    ->icon('heroicon-o-wallet')
                    ->url(fn($record) => static::getUrl('wallet', ['record' => $record->id]))
                    ->label(__('menu.wallet')),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array {
        return [
            'index' => ListProviders::route('/'),
            'create' => CreateProvider::route('/create'),
            'edit' => EditProvider::route('/{record}/edit'),
            'wallet' => WalletPage::route('{record}/wallet'),
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
