<?php

namespace App\UtilitiesModule\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use App\DefaultPanel\Settings\LandingSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Contracts\Support\Htmlable;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;
use Illuminate\Support\HtmlString;
use App\ContentModule\Models\Page;
use App\ContentModule\Resources\BannerResource;

class ManageLanding extends SettingsPage {
    use HasPageShield;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $slug = 'settings/landing';

    protected static string $settings = LandingSettings::class;

    public function form(Schema $schema): Schema {
        return $schema
            ->components([
                FileUpload::make('logos.ar')
                    ->label(__('forms.fields.logo_in_arabic'))
                    ->required(),
                FileUpload::make('logos.en')
                    ->label(__('forms.fields.logo_in_english'))
                    ->required(),
                Section::make("app_download")
                    ->label(__("sections.app_download"))
                    ->schema([
                        Tabs::make()->schema([
                            Tab::make(__("panel.languages.arabic"))->schema([
                                TextInput::make('title')
                                    ->label(__('forms.fields.address'))
                                    ->required(),
                                FileUpload::make('image')->label(__("forms.fields.image")),
                                Textarea::make('description')
                                    ->label(__('forms.fields.description'))
                                    ->required()
                            ])->statePath("app_download.ar"),

                            Tab::make(__("panel.languages.english"))->schema([
                                TextInput::make('title')
                                    ->label(__('forms.fields.address'))
                                    ->required(),
                                FileUpload::make('image')->label(__("forms.fields.image")),
                                Textarea::make('description')
                                    ->label(__('forms.fields.description'))
                                    ->required()
                            ])->statePath("app_download.en")
                        ])
                        ,

                    ])->statePath('content'),
                
                
                Section::make("testimonials")
                    ->label(__("sections.testimonials"))
                    ->schema([
                        Repeater::make('testimonials')
                            ->label('')
                            ->schema([
                                Tabs::make()->schema([
                                    Tab::make(__('panel.languages.arabic'))->schema([
                                        TextInput::make('name_ar')
                                            ->label(__('sections.testimonial_name'))
                                            ->required(),
                                        Textarea::make('text_ar')
                                            ->label(__('sections.testimonial_content')),
                                    ]),
                                    Tab::make(__('panel.languages.english'))->schema([
                                        TextInput::make('name_en')
                                            ->label(__('sections.testimonial_name'))
                                            ->required(),
                                        Textarea::make('text_en')
                                            ->label(__('sections.testimonial_content')),
                                    ]),
                                ])->columnSpanFull(),
                                FileUpload::make('avatar')
                                    ->label(__('sections.testimonial_avatar'))
                                    ->image(),
                                Select::make('rating')
                                    ->label(__('sections.testimonial_rating'))
                                    ->options([
                                        1 => '1',
                                        2 => '2',
                                        3 => '3',
                                        4 => '4',
                                        5 => '5',
                                    ])
                                    ->default(5)
                                    ->required(),
                                DatePicker::make('date')
                                    ->label(__('sections.testimonial_date'))
                                    ->required(),
                                Select::make('type')
                                    ->label(__('sections.testimonial_type'))
                                    ->options([
                                        'text' => __('sections.testimonial_type_text'),
                                        'image' => __('sections.testimonial_type_image'),
                                        'video' => __('sections.testimonial_type_video'),
                                        'audio' => __('sections.testimonial_type_audio'),
                                    ])
                                    ->default('text')
                                    ->live()
                                    ->required(),
                                FileUpload::make('media')
                                    ->label(__('sections.testimonial_media'))
                                    ->visible(fn(Get $get) => $get('type') === 'image')
                                    ->acceptedFileTypes(['image/*']),
                                FileUpload::make('media')
                                    ->label(__('sections.testimonial_media'))
                                    ->visible(fn(Get $get) => $get('type') === 'video')
                                    ->acceptedFileTypes(['video/*']),
                                FileUpload::make('media')
                                    ->label(__('sections.testimonial_media'))
                                    ->visible(fn(Get $get) => $get('type') === 'audio')
                                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/aac', 'audio/x-mpeg']),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['name_ar'] ?? $state['name_en'] ?? null),
                    ])->statePath('content'),

                Section::make('site_pages')
                    ->label(__('sections.site_pages'))
                    ->schema([
                        Fieldset::make(__('sections.site_pages'))
                            ->schema([
                                Select::make('about_us')
                                    ->label(__('forms.fields.about_us'))
                                    ->options(Page::pluck('title', 'id')->toArray())
                                    ->searchable(),
                                Select::make('terms_and_conditions')
                                    ->label(__('forms.fields.terms_and_conditions'))
                                    ->options(Page::pluck('title', 'id')->toArray())
                                    ->searchable(),
                                Select::make('privacy_policy')
                                    ->label(__('forms.fields.privacy_policy'))
                                    ->options(Page::pluck('title', 'id')->toArray())
                                    ->searchable(),
                            ])
                            ->columns(2),
                    ])
                    ->statePath('content.site_pages')
                    ->collapsible(),

            ]);
    }

    public static function getNavigationLabel(): string {
        return __("menu.landing_page");
    }

    public function getHeading(): string|Htmlable {
        return __('sections.landing_page');
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.settings');
    }

    public function getTitle(): string|Htmlable {
        return __('sections.landing_page');
    }

    public function getBreadcrumbs(): array {
        return [
            null => static::getNavigationGroup(),
            static::getUrl() => static::getNavigationLabel(),
        ];
    }
    public  function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
