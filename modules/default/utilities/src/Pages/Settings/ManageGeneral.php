<?php

namespace App\UtilitiesModule\Pages\Settings;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Resources\Components\Tab;
use Illuminate\Contracts\Support\Htmlable;
use App\ContentModule\Models\Page;
use App\DefaultPanel\Forms\Components\SelectFontAwesomeIcon;
use App\DefaultPanel\Settings\GeneralSettings;

class ManageGeneral extends SettingsPage {
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $settings = GeneralSettings::class;
    protected static ?string $slug = 'settings/general';
    protected static ?int $navigationSort = 1;

    public function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make("General")->schema([
                    FileUpload::make('app_logo'),
                    TextInput::make('app_name')
                        ->required(),
                    TextInput::make('app_email')
                        ->email()
                        ->required(),
                    TextInput::make('app_address')
                        ->required(),

                    TextInput::make('app_phone')
                        ->type('number')
                        ->numeric()
                        ->required(),
                    TextInput::make('app_whatsapp')
                        ->type('number')
                        ->numeric()
                        ->required(),
                    TextInput::make('commercial_register')
                        ->type('number')
                        ->numeric()
                        ->required(),
                    TextInput::make('tax_number')
                        ->type('number')
                        ->numeric()
                        ->required(),
                    TextInput::make('reservations_fess')
                        ->label(__('forms.fields.reservations_fees'))
                        ->type('number')
                        ->step(0.01) 
                        ->suffix(__("forms.suffixes.sar"))
                        ->required(),

                    TextInput::make('app_percentage')
                        ->suffix(__("forms.suffixes.percentage"))
                        ->type('number')
                        ->step(0.01)
                        ->required(),

                    Forms\Components\Toggle::make('enabled_free_fees_in_first_reservation'),
                    Forms\Components\Toggle::make('enabled_whatsapp_icon'),

                ]),

                Forms\Components\Section::make("applications_links")->schema([

                    Forms\Components\Fieldset::make()->label(__("forms.fields.client_app"))->schema([
                        TextInput::make('applications_links.client.google_play_link')
                            ->label(__('forms.fields.google_play_link'))
                            ->url()
                            ->required(),
                        TextInput::make('applications_links.client.apple_store_link')
                            ->label(__('forms.fields.apple_store_link'))
                            ->url()
                            ->required(),
                    ]),
                    Forms\Components\Fieldset::make()->label(__("forms.fields.provider_app"))->schema([
                        TextInput::make('applications_links.provider.google_play_link')
                            ->label(__('forms.fields.google_play_link'))
                            ->url()
                            ->required(),
                        TextInput::make('applications_links.provider.apple_store_link')
                            ->label(__('forms.fields.apple_store_link'))
                            ->url()
                            ->required(),
                    ])
                ])->columns(1),
                Forms\Components\Section::make("app_pages")->schema([
                    Forms\Components\Fieldset::make(__("sections.app_pages"))
                        ->schema([
                            Select::make('app_pages.about_us')
                                ->label(__('forms.fields.about_us'))
                                ->options(Page::pluck('title', 'id')->toArray()),

                            Select::make('app_pages.terms_and_conditions')
                                ->options(Page::pluck('title', 'id')->toArray())
                                ->label(__('forms.fields.terms_and_conditions')),
                            Select::make('app_pages.privacy_policy')
                                ->options(Page::pluck('title', 'id')->toArray())
                                ->label(__('forms.fields.privacy_policy')),

                            Select::make('app_pages.return_policy')
                                ->options(Page::pluck('title', 'id')->toArray())
                                ->label(__('forms.fields.return_policy')),
                        ]),
                    Forms\Components\Fieldset::make(__("sections.labs_pages"))
                        ->schema([
                            Select::make('about_us')
                                ->label(__('forms.fields.about_us'))
                                ->options(Page::pluck('title', 'id')->toArray()),

                            Select::make('terms_and_conditions')
                                ->options(Page::pluck('title', 'id')->toArray())
                                ->label(__('forms.fields.terms_and_conditions')),
                            Select::make('privacy_policy')
                                ->options(Page::pluck('title', 'id')->toArray())
                                ->label(__('forms.fields.privacy_policy')),

                        ])->statePath('provider_pages'),

                ])
                    ->columns(1)
                    ->collapsible(),
                Forms\Components\Section::make("points")->schema([

                    TextInput::make('points.customer_reserve_action')
                        ->label(__('forms.fields.points_count_when_customer_reserve'))
                        ->type('number')
                        ->required()
                    ,
                    TextInput::make('points.customer_register_action')
                        ->label(__('forms.fields.points_count_when_customer_register'))
                        ->type('number')
                        ->required()
                    ,
                    TextInput::make('points.today_dob_customer')
                        ->label(__('forms.fields.points_count_when_customer_dob'))
                        ->type('number')
                        ->required(),

                ]),
                Forms\Components\Section::make("social_links")->schema([

                    Repeater::make("social_links")
                        ->label('')
                        ->schema([
                            SelectFontAwesomeIcon::make('icon')
                                ->searchable()
                                ->allowHtml(),

                            TextInput::make('link')
                                ->url()
//                                ->required()
                            ,
                        ])

                ])
                    ->collapsible()
//                    ->collapsed()
            ]);
    }

    public static function getNavigationLabel(): string {
        return __("menu.general");
    }

    public function getHeading(): string|Htmlable {
        return __('sections.global_settings');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.settings');
    }

    public function getTitle(): string|Htmlable {
        return __('sections.global_settings');
    }

    public function workingDaysSchema(): Repeater {
        $schema = [];
        foreach (['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {

            $schema [] = Group::make([
                Checkbox::make("$day.status")->label(__("forms.fields.weekdays.$day")),
                Hidden::make("$day.day_name")->default($day),
                TextInput::make("$day.from")->type('time')->label('')->default("00:00"),
                TextInput::make("$day.to")->type('time')->label('')->default("23:59"),
            ])->columns(3);
        }
        return Repeater::make('working_days')
            ->schema($schema)
            ->reorderable(false)
            ->deletable(false)
            ->minItems(1)
            ->maxItems(1);
    }

    public function getBreadcrumbs(): array {
        return [
            null => static::getNavigationGroup(),
            static::getUrl() => static::getNavigationLabel(),
        ];
    }
}
