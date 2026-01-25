<?php

namespace App\UtilitiesModule\Pages\Settings;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Illuminate\Contracts\Support\Htmlable;
use App\DefaultPanel\Settings\DeveloperSetting;

class ManageDeveloper extends SettingsPage {
    use HasPageShield;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = DeveloperSetting::class;
    protected static ?string $slug = 'settings/developer';

    public function form(Schema $schema): Schema {
        return $schema
            ->components([
                Toggle::make('debug_mode')
                    ->onColor('success')
                    ->offColor('danger'),
                Toggle::make('otp_code_is_random')
                    ->onColor('success')
                    ->offColor('danger') ,
            ])->columns(1);
    }


    public function getTitle(): string|Htmlable {
        return __('sections.developer_settings');
    }

    public function getHeading(): string|Htmlable {
        return __('sections.developer_settings');
    }

    public static function getNavigationLabel(): string {
        return __("sections.developer_settings");
    }

    public static function getNavigationGroup(): ?string {
        return __('menu.settings');
    }

    public static function shouldRegisterNavigation(): bool {
        return auth()->user()->email == 'ahmed.mostafa.dev.eg@gmail.com';
    }

}
