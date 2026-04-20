<?php

namespace App\ProviderPanel\Filament\Pages;

use App\ContentModule\Models\Category;
use App\ContentModule\Models\City;
use App\ContentModule\Models\Country;
use App\ContentModule\Models\State;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Forms\Components\SafeRepeater;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use libphonenumber\PhoneNumberType;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class EditProfilePage extends Page
{
    use InteractsWithFormActions;

    public $record;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.edit-profile';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('record')
            ->model(provider()->user)
            ->components([
                Tabs::make('')->schema([
                    Tab::make(__('sections.basic_information'))->schema([
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->nullable(),

                        TextInput::make('data.first_name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $get) => $set('name', $get('data.first_name').' '.$get('data.last_name')))
                            ->required()
                            ->minLength(3),

                        TextInput::make('data.last_name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set, $get) => $set('name', $get('data.first_name').' '.$get('data.last_name')))
                            ->minLength(3)
                            ->required(),

                        Hidden::make('name'),

                        PhoneInput::make('phone')
                            ->required()
                            ->onlyCountries(['SA'])
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
                            ->autocomplete('off'),

                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->confirmed()
                            ->autocomplete('new-password'),

                        TextInput::make('password_confirmation')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->autocomplete('off'),

                        Select::make('gender')
                            ->required()
                            ->options([
                                'male' => __('panel.enums.male'),
                                'female' => __('panel.enums.female'),
                            ]),

                    ]),
                    Tab::make(__('sections.provider_information'))->schema([
                        Group::make()->schema([
                            Tabs::make('Label')
                                ->tabs([
                                    Tab::make(__('panel.languages.arabic'))
                                        ->schema([
                                            TextInput::make('name.ar')
                                                ->label(__('forms.fields.provider_name'))
                                                ->required(),
                                            Textarea::make('bio.ar')
                                                ->label(__('forms.fields.bio'))
                                                ->required(),
                                        ]),
                                    Tab::make(__('panel.languages.english'))
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
                                ->collection('images')
                                ->required(),
                            SpatieMediaLibraryFileUpload::make('commercial_register')
                                ->collection('commercial_register')
                                ->nullable(),
                            Select::make('category_id')
                                ->required()
                                ->options(function ($get, $set, $record) {

                                    return Category::pluck('name', 'id');
                                }),
                            Select::make('country_id')
                                ->live()
                                ->required()
                                ->formatStateUsing(fn ($record) => $record?->city?->state->country_id)
                                ->options(function ($get, $set, $record) {

                                    return Country::pluck('name', 'id');
                                }),

                            Select::make('state_id')
                                ->live()
                                ->required()
                                ->formatStateUsing(fn ($record) => $record?->city?->state_id)
                                ->options(function ($get, $set, $record) {

                                    return State::where('country_id', $get('country_id'))->pluck('name', 'id');
                                }),

                            Select::make('city_id')
                                ->label(__('forms.fields.city_id'))
                                ->options(fn ($get) => City::where('state_id', $get('state_id'))->pluck('name', 'id')),

                            Map::make('location')
                                ->live()
                                ->geolocate()
                                ->geolocateLabel(__('panel.messages.locate_my_location'))
                                ->formatStateUsing(function ($record) {
                                    if (! $record || ! $record?->location) {
                                        return;
                                    }

                                    return [
                                        'lat' => Point::fromWkt($record->location)->latitude,
                                        'lng' => Point::fromWkt($record->location)->longitude,
                                    ];
                                })
                                ->autocomplete('address_name')
                                ->debug()
                                ->drawingField('boundaries')
                                ->defaultLocation([24.7136, 46.6753])
                                ->draggable()
                                ->clickable(),
                            Section::make(__('sections.working_times'))->schema(GeneralSettings::daysListSchema())
                                ->statePath('meta_data.days_list'),

                        ])
                            ->relationship('provider')
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, Group $component): array {
                                $livewire = $component->getLivewire();
                                $providerFromForm = data_get($livewire->record ?? [], 'provider', []);
                                $incomingMeta = $providerFromForm['meta_data'] ?? null;
                                if (! is_array($incomingMeta)) {
                                    return $data;
                                }
                                $record = $component->getCachedExistingRecord();
                                // Do not use array_replace_recursive: numeric portfolio_albums rows merge by index and
                                // duplicate / resurrect removed albums. Replace whole top-level meta keys instead.
                                $data['meta_data'] = array_replace(
                                    $record?->meta_data ?? [],
                                    $incomingMeta
                                );

                                return $data;
                            }),

                    ]),
                    Tab::make(__('sections.portfolio_gallery'))->schema([
                        Group::make()->schema([
                            SafeRepeater::make('portfolio_albums')
                                ->statePath('meta_data.portfolio_albums')
                                ->label(__('forms.fields.portfolio'))
                                ->helperText(__('forms.helpers.portfolio_upload'))
                                ->addActionLabel(__('forms.fields.add_album'))
                                ->reorderable()
                                ->defaultItems(0)
                                ->schema([
                                    Hidden::make('album_id')
                                        ->default(fn () => \Illuminate\Support\Str::random(12))
                                        ->dehydrated(),
                                    Tabs::make('')->tabs([
                                        Tab::make(__('panel.languages.arabic'))->schema([
                                            TextInput::make('title.ar')
                                                ->label(__('forms.fields.album_title')),
                                        ]),
                                        Tab::make(__('panel.languages.english'))->schema([
                                            TextInput::make('title.en')
                                                ->label(__('forms.fields.album_title')),
                                        ]),
                                    ])->columnSpanFull(),
                                    SpatieMediaLibraryFileUpload::make('media')
                                        ->collection('portfolio')
                                        ->multiple()
                                        ->reorderable()
                                        ->customProperties(function ($file, \Filament\Schemas\Components\Utilities\Get $get) {
                                            $albumId = $get('album_id');

                                            return ['album_id' => $albumId ?? \Illuminate\Support\Str::random(12)];
                                        })
                                        ->filterMediaUsing(function ($media, \Filament\Schemas\Components\Utilities\Get $get) {
                                            $albumId = $get('album_id');
                                            if (! $albumId) {
                                                return $media->filter(fn ($m) => false);
                                            }

                                            return $media->filter(fn ($m) => ($m->getCustomProperty('album_id') ?? '') === $albumId);
                                        })
                                        ->acceptedFileTypes([
                                            'image/jpeg',
                                            'image/png',
                                            'image/webp',
                                            'image/gif',
                                            'video/mp4',
                                            'video/mpeg',
                                            'audio/mpeg',
                                            'audio/mp3',
                                            'audio/wav',
                                        ])
                                        ->nullable(),
                                ])
                                ->columns(1),
                        ])->relationship('provider'),
                    ]),
                    Tab::make(__('sections.seo_meta'))->schema([
                        Group::make()->schema([
                            Tabs::make('seo_languages')
                                ->tabs([
                                    Tab::make(__('panel.languages.arabic'))
                                        ->schema([
                                            Textarea::make('meta_description.ar')
                                                ->label(__('forms.fields.meta_description'))
                                                ->rows(3),
                                            TagsInput::make('meta_keywords.ar')
                                                ->label(__('forms.fields.meta_keywords'))
                                                ->separator(',')
                                                ->splitKeys([',', 'Enter'])
                                                ->placeholder(__('forms.placeholders.meta_keywords_tags')),
                                        ]),
                                    Tab::make(__('panel.languages.english'))
                                        ->schema([
                                            Textarea::make('meta_description.en')
                                                ->label(__('forms.fields.meta_description'))
                                                ->rows(3),
                                            TagsInput::make('meta_keywords.en')
                                                ->label(__('forms.fields.meta_keywords'))
                                                ->separator(',')
                                                ->splitKeys([',', 'Enter'])
                                                ->placeholder(__('forms.placeholders.meta_keywords_tags')),
                                        ]),
                                ]),
                        ])->relationship('provider'),
                    ]),
                    Tab::make(__('sections.bank_account_information'))->schema([
                        Group::make()->schema([
                            TextInput::make('bank_name'),
                            TextInput::make('account_name'),
                            TextInput::make('account_number'),
                            TextInput::make('iban'),
                        ])->relationship('bankAccount'),

                    ]),
                    Tab::make(__('sections.settings'))->schema([
                        Group::make()->schema([
                            Tabs::make('tabs')
                                ->schema([
                                    Tab::make(__('panel.languages.arabic'))->schema([
                                        Textarea::make('ar.text_when_order_completed'),
                                    ]),
                                    Tab::make(__('panel.languages.english'))->schema([
                                        Textarea::make('en.text_when_order_completed'),
                                    ]),
                                ])->statePath('texts'),

                            Select::make('reservation_flow')
                                ->options([
                                    'total' => __('panel.messages.pay_reservation_totals'),
                                    'fees' => __('panel.messages.pay_reservation_fees'),
                                ]),

                        ])->statePath('options'),

                    ]),
                ]),
            ])
            ->columns(1);
    }

    public function mount()
    {
        $this->record = provider()->user;

        $this->form->fill([
            'avatar' => $this->record->getFirstMediaUrl(),
            ...$this->record->toArray(),
            'provider' => [
                ...provider()->toArray(),
                'state_id' => provider()->city->state_id,
                'country_id' => provider()->city->state->country_id,
            ],
            'bankAccount' => $this->record->bankAccount,
            'options' => $this->record->options,
        ]);

        $this->normalizePortfolioAlbumsStatePath();
    }

    /**
     * Keep one repeater row per album_id (fixes duplicates from legacy array_replace_recursive merges).
     */
    protected function normalizePortfolioAlbumsStatePath(): void
    {
        $albums = data_get($this->record, 'provider.meta_data.portfolio_albums');
        if (! is_array($albums)) {
            return;
        }
        $normalized = collect($albums)
            ->filter(fn ($item) => is_array($item))
            ->unique('album_id')
            ->values()
            ->all();
        data_set($this->record, 'provider.meta_data.portfolio_albums', $normalized);
    }

    public function submit(): void
    {
        $this->form->getState(afterValidate: function () {});
        $data = $this->record;
        $this->form->model->update([
            'name' => $this->record['name'],
            'phone' => $this->record['phone'],
            'email' => $this->record['email'],
            'gender' => $this->record['gender'],
            'password' => $this->record['password'],
            'data' => $data['data'],
        ]);
        $this->form->model->options()->update(collect($data['options'])->only(['texts', 'reservations_fees', 'reservation_flow', 'enabled_free_fees_in_first_reservation'])->toArray());

        $providerPayload = collect($this->record['provider'] ?? [])->only([
            'name',
            'bio',
            'city_id',
            'category_id',
            'meta_description',
            'meta_keywords',
            'meta_data',
        ])->toArray();

        $portfolioAlbums = collect(data_get($providerPayload, 'meta_data.portfolio_albums', []))
            ->filter(fn ($item) => is_array($item))
            ->unique('album_id')
            ->values()
            ->all();
        data_set($providerPayload, 'meta_data.portfolio_albums', $portfolioAlbums);
        data_set($this->record, 'provider.meta_data.portfolio_albums', $portfolioAlbums);

        $this->form->model->provider()->update([
            ...$providerPayload,
            'location' => (new Point($this->record['provider']['location']['lat'], $this->record['provider']['location']['lng']))->toSqlExpression($this->form->model->getConnection()),
        ]);

        // Removing a portfolio repeater row only updates form state; media rows remain until removed.
        $providerModel = $this->form->model->provider()->first();
        if ($providerModel) {
            $allowedAlbumIds = collect(data_get($this->record, 'provider.meta_data.portfolio_albums', []))
                ->pluck('album_id')
                ->filter(fn ($id) => $id !== null && $id !== '')
                ->unique();
            foreach ($providerModel->getMedia('portfolio') as $mediaItem) {
                $albumId = $mediaItem->getCustomProperty('album_id');
                if ($albumId !== null && $albumId !== '' && ! $allowedAlbumIds->contains($albumId)) {
                    $mediaItem->delete();
                }
            }
        }

        foreach ($this->record['provider']['image'] ?? [] as $media) {
            if ($media instanceof TemporaryUploadedFile) {
                $this->form->model?->provider?->clearMediaCollection();
                $this->form->model?->provider?->addMedia($media)->toMediaCollection();
            }

        }
        foreach ($this->record['provider']['images'] ?? [] as $media) {
            if ($media instanceof TemporaryUploadedFile) {
                $this->form->model?->provider?->clearMediaCollection('images');
                $this->form->model?->provider?->addMedia($media)->toMediaCollection('images');
            }

        }
        foreach ($this->record['provider']['commercial_register'] ?? [] as $media) {
            if ($media instanceof TemporaryUploadedFile) {
                $this->form->model?->provider?->clearMediaCollection('commercial_register');
                $this->form->model?->provider?->addMedia($media)->toMediaCollection('commercial_register');
            }

        }
        foreach ($this->record['avatar'] ?? [] as $media) {
            if ($media instanceof TemporaryUploadedFile) {
                $this->form->model?->clearMediaCollection();
                $this->form->model->addMedia($media)->toMediaCollection();
            }
        }
        $this->form->model->bankAccount()->update(collect($data['bankAccount'])->only(['bank_name', 'account_name', 'account_number', 'iban'])->toArray());
        Notification::make()
            ->title(__('panel.messages.success'))
            ->success()
            ->send();
    }

    public function getTitle(): string
    {
        return __('menu.edit_profile');
    }

    public static function getNavigationLabel(): string
    {
        return __('menu.edit_profile');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.settings');
    }
}
