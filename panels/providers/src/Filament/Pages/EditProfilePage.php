<?php

namespace App\ProviderPanel\Filament\Pages;

use App\CatalogModule\Models\Specialization;
use App\ContentModule\Models\City;
use App\ContentModule\Models\Contact;
use App\ContentModule\Models\Country;
use App\ContentModule\Models\Language;
use App\ContentModule\Models\Nationality;
use App\ContentModule\Models\State;
use App\ContentModule\Models\Title;
use App\DefaultPanel\Enum\ContactSourceEnum;
use App\UsersModule\Models\User\Doctor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use libphonenumber\PhoneNumberType;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Str;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class EditProfilePage extends Page {
    public $record;


    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.edit-profile';

    public function form(Form $form): Form {
        return $form
            ->statePath('record')
            ->model(provider()->user)
            ->schema([
                Group::make([
                    Section::make("basic_information")->schema([
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->nullable()
                            ->columnSpan(2),

                        TextInput::make('name')
                            ->required()
                            ->columnSpan(1)
                            ->minLength(3),


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
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->autocomplete("off"),
                        Select::make('gender')
                            ->options([
                                'male' => __("panel.enums.male"),
                                'female' => __("panel.enums.female"),
                            ]),

                        TextInput::make('password')
                            ->password()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->confirmed()
                            ->autocomplete("new-password"),

                        TextInput::make('password_confirmation')
                            ->password()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->autocomplete("off"),


                    ])
                        ->columns(2),

                ])
                    ->columnSpan(2),
                Group::make([
                    Section::make("lab_information")
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('logo')
                                ->columnSpan(2),

                            TextInput::make('title')
                                ->required()
                                ->columnSpan(2),

                            Textarea::make('description')
                                ->required()
                                ->columnSpan(2),

                            TextInput::make('doctor_name')
                                ->required()
                                ->columnSpan(1),


                            TextInput::make('license_number')
                                ->required()
                                ->columnSpan(1),


                            TextInput::make('tax_number')
                                ->required()
                                ->columnSpan(1),

                            Select::make('state_id')
                                ->live()
                                ->formatStateUsing(fn($record) => $record?->city?->state_id)
                                ->options(State::pluck('name', 'id')),

                            Select::make('city_id')
                                ->label(__('forms.fields.city_id'))
                                ->options(fn($get) => City::where('state_id', $get('state_id'))->pluck('name', 'id')),

                            Map::make('location')
                                ->live()
                                ->formatStateUsing(function ($record) {
                                    if (!$record || !$record->location) return;
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
                                ->clickable()
                                ->drawingModes(fn($get) => $get('boundaries') ? [] : ['marker' => false, 'circle' => false, 'polygon' => true, 'polyline' => false, 'rectangle' => false])
                                ->drawingControl()
                                ->columnSpan(2),
                            SpatieMediaLibraryFileUpload::make('license_image')
                                ->collection('license_image')
                                ->columnSpan(2),
                            SpatieMediaLibraryFileUpload::make('images')
                                ->collection('images')
                                ->multiple()
                                ->columnSpan(2),

                            Fieldset::make("times")
                                ->label(__('sections.times'))
                                ->schema([


                                    Select::make('meta_data.session_duration')
                                        ->live()
                                        ->options([
                                            '15' => __("panel.enums.15_minutes"),
                                            '30' => __("panel.enums.30_minutes"),
                                            '45' => __("panel.enums.45_minutes"),
                                        ])
                                        ->default(45),

                                    ...self::timesListSchema(),

                                ])
                                ->columns(1),


                        ])
                        ->columns(2),

                ])
                    ->relationship('lab')
                    ->columnSpan(3)

            ])->columns(5);
    }

    public function mount() {
        $this->record = provider()->user;

        $this->form->fill([
            ...$this->record->toArray(),
            'meta_data' => [
                'times_list' => $this->record->lab->meta_data['times_list'] ?? [],
                'session_duration'=>$this->record->lab->meta_data['session_duration'] ?? 0
            ],

        ]);


    }


    static public function timesListSchema(): array {

        $schema = [];
        foreach (['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $index=> $day) {

            $schema [] =
                Group::make([
                    Checkbox::make("$index.status")
                        ->label(__("forms.fields.weekdays.$day")),

                    Hidden::make("$index.day_name")
                        ->default($day),
                    CheckboxList::make("$index.slot")
                        ->live()
                        ->label(__('forms.fields.slots'))
                        ->options(function (Get $get) {

                            $startPeriod = Carbon::parse('00:00');
                            $endPeriod = Carbon::parse('23:59');
                            $interval = ($get('../../../meta_data.session_duration') ?? 45) . " minutes";
                            $period = CarbonPeriod::create($startPeriod, $interval, $endPeriod);
                            $hours = [];
                            foreach ($period as $date) {
                                $hours[] = $date->format('H:i');
                            }

                            return collect($hours)->sliding()->map(fn($period) => $period->values())->mapWithKeys(fn($item, $key) => [$item[0] . " - " . $item[1] => $item[0] . " - " . $item[1]]);
                        })
                        ->columns(2)
                ])->columns(2);

        }
        return [
            Repeater::make('meta_data.times_list')
                ->label('')
                ->schema($schema)
                ->reorderable(false)
                ->deletable(false)
                ->minItems(1)
                ->maxItems(1)
        ];
    }

    public function submit() {
        $user = $this->form->model->update([
            'name' => $this->record['name'],
            'phone' => $this->record['phone'],
            'email' => $this->record['email'],
            'gender' => $this->record['gender'],
            'city_id' => $this->record['city_id'],
            'password' => $this->record['password'],
        ]);
        $this->form->model->provider()->update([
                ...collect($this->record['lab'])->only(['title','description','doctor_name', 'license_number', 'tax_number','city_id'])->toArray(),
                'location'=>(new Point($this->record['lab']['location']['lat'], $this->record['lab']['location']['lng']))->toSqlExpression($this->form->model->getConnection()),
                'meta_data' => [
                    'session_duration'=>$this->record['lab']['meta_data']['session_duration']??0,
                    'times_list'=>$this->record['lab']['meta_data']['times_list']??[]
                ],
            ]);


        foreach ($this->record['lab']['logo'] ?? [] as $media) {
            if ($media instanceof TemporaryUploadedFile) {
                $this->form->model?->lab?->clearMediaCollection();
                $this->form->model?->lab?->addMedia($media)->toMediaCollection();
            }

        }
        foreach ($this->record['avatar'] ?? [] as $media) {
            if ($media instanceof TemporaryUploadedFile) {
                $this->form->model?->clearMediaCollection();
                $this->form->model->addMedia($media)->toMediaCollection();
            }
        }
        foreach ($this->record['license_image'] ?? [] as $media) {
            if ($media instanceof TemporaryUploadedFile) {
                $this->form->model?->lab?->clearMediaCollection("license_image");
                $this->form->model->lab->addMedia($media)->toMediaCollection("license_image");
            }
        }
        foreach ($this->record['images'] ?? [] as $media) {
            if ($media instanceof TemporaryUploadedFile) {
                $this->form->model?->lab?->clearMediaCollection("images");
                $this->form->model->lab?->addMedia($media)->toMediaCollection("images");
            }

        }
        Notification::make()
            ->title(__('panel.messages.success'))
            ->success()
            ->send();
    }

    public function getTitle(): string {
        return __('menu.edit_profile');
    }


    public static function getNavigationLabel(): string {
        return __('menu.edit_profile');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.settings');
    }

}
