<?php

namespace App\UtilitiesModule\Pages\Settings;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;
use App\DefaultPanel\Settings\ThirdPartySettings;
use Filament\Support\Enums\Width;
class ManageThirdParty extends SettingsPage {
    use HasPageShield;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-link';
    protected static string $settings = ThirdPartySettings::class;
    protected static ?string $slug = 'settings/third-party';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation=false;
    public function form(Schema $schema): Schema {
        return $schema
            ->components([
                Section::make("General")->schema([

                    TextInput::make('firebase_server_key')
                        ->columnSpan(['xl' => 2]),

                    TextInput::make('firebase_server_id')
                        ->columnSpan(['xl' => 2]),

                    TextInput::make('google_map_key')
                        ->columnSpan(['xl' => 2]),
                ])
            ]);
    }
    public static function getNavigationLabel(): string {
        return __("menu.third_party");
    }
    public static function getNavigationGroup(): ?string {
        return __('menu.settings');
    }
    public function getHeading(): string|Htmlable {
        return __('sections.manage_third_party');
    }
    public function getTitle(): string|Htmlable {
        return __('sections.manage_third_party');
    }
    public function getBreadcrumbs(): array {
        return [
            null =>static::getNavigationGroup(),
            static::getUrl() => static::getNavigationLabel(),
        ];
    }
    public  function getMaxContentWidth(): Width
    {
        return Width::Full;
    }
}
