<?php

namespace App\UtilitiesModule\Pages;

use App\DefaultPanel\Settings\LandingSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Contracts\Support\Htmlable;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Repeater;


class ManageLanding extends SettingsPage {
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $slug = 'settings/landing';

    protected static string $settings = LandingSettings::class;

    public function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make("header")
                    ->label(__("sections.header"))
                    ->schema([
                        TextInput::make('header.title')
                            ->label(__('forms.fields.title'))
                            ->required(),
                        Textarea::make('header.description')
                            ->label(__('forms.fields.description'))
                            ->required(),
                        FileUpload::make('header.image')->label(__("forms.fields.image")),
                    ])->statePath('content'),
                Forms\Components\Section::make("about_us")
                    ->label(__("sections.about_us"))
                    ->schema([
                        Textarea::make('about')
                            ->label(__('forms.fields.description'))
                            ->required(),
                    ])->statePath('content'),
                Forms\Components\Section::make("our_features")
                    ->label(__("sections.our_features"))
                    ->schema([
                        Repeater::make('features')->schema([
                            TextInput::make('title')
                                ->label(__('forms.fields.title'))
                                ->required(),
                            Textarea::make('description')
                                ->label(__('forms.fields.description'))
                                ->required(),
                            FileUpload::make('image')->label(__("forms.fields.image")),
                            Repeater::make('pros')->schema([
                                Tabs::make('tabs')->schema([
                                    Tabs\Tab::make(__("panel.languages.arabic"))->schema([
                                            TextInput::make('title')
                                        ])->statePath("ar"),
                                    Tabs\Tab::make(__("panel.languages.english"))->schema([
                                            TextInput::make('title')
                                        ])->statePath("en")
                                ])
                            ])

                        ])
                    ])->statePath('content'),
                Forms\Components\Section::make("footer")
                    ->label(__("sections.footer"))
                    ->schema([
                        TextInput::make('footer.title')
                            ->label(__('forms.fields.title'))
                            ->required(),
                        Textarea::make('footer.description')
                            ->label(__('forms.fields.description'))
                            ->required(),
                    ])->statePath('content'),

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
}
