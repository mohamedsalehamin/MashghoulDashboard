<?php

namespace App\ProviderPanel;

use App\DefaultPanel\Settings\GeneralSettings;
use App\ProviderPanel\Filament\Pages\AboutUsPage;
use App\ProviderPanel\Filament\Pages\ContactPage;
use App\ProviderPanel\Filament\Pages\FaqsPage;
use App\ProviderPanel\Filament\Pages\LoginPage;
use App\ProviderPanel\Filament\Pages\PrivacyAndPolicyPage;
use App\ProviderPanel\Filament\Pages\RequestPasswordReset;
use App\ProviderPanel\Filament\Pages\TermsAndConditionsPage;
use App\CatalogModule\Resources\ReservationResource;
use App\CatalogModule\Resources\ReservationResource\Widgets\ReservationStats;

use App\ProviderPanel\Filament\Resources\CustomerResource;
use App\ProviderPanel\Filament\Resources\NotificationResource;
use App\ProviderPanel\Filament\Resources\RateResource;
use App\ProviderPanel\Filament\Resources\SeatResource;
use App\ProviderPanel\Filament\Resources\ServiceResource;
use App\ProviderPanel\Filament\Resources\WalletResource;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Storage;

class ProviderPanelServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel {
        return $panel
            ->domain('portal.mashghoul.test')
            ->font('cairo', 'https://fonts.googleapis.com/css2?family=Cairo:wght@700;800&display=swap')
            ->id('lab-panel')
            ->path('')
            ->login(LoginPage::class)
            ->passwordReset(RequestPasswordReset::class)
            ->navigationGroups([
                NavigationGroup::make()->label(fn(): string => __('menu.subscriptions')),
                NavigationGroup::make()->label(fn(): string => __('menu.pages')),
                NavigationGroup::make()->label(fn(): string => __('menu.settings')),
            ])
//            ->spa()
            ->userMenuItems([
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->pages([
                Dashboard::class,
                AboutUsPage::class,
                TermsAndConditionsPage::class,
                PrivacyAndPolicyPage::class,
                FaqsPage::class,
                ContactPage::class,
                Filament\Pages\EditProfilePage::class
            ])
            ->brandLogo(function (GeneralSettings $settings) {
                return Storage::disk('public')->exists($settings->app_logo ?? 'null') ? asset("storage/$settings->app_logo") : '';

            })
            ->brandName('Mashghoul')
            ->plugins([
                FilamentFullCalendarPlugin::make()
                    ->selectable(false)
                    ->editable(false)
                    ->locale(app()->getLocale()),


//                FilamentShieldPlugin::make(),
                SpatieLaravelTranslatablePlugin::make()->defaultLocales(['en', 'ar']),

                ThemesPlugin::make()->canViewThemesPage(fn() => auth()?->user()?->email === 'ahmed.mostafa.dev.eg@gmail.com'),

            ])
            ->databaseNotifications(true)
            ->sidebarCollapsibleOnDesktop()

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetTheme::class,
            ])
            ->widgets([
                ReservationStats::class,
            ])
            ->resources([

                \App\ProviderPanel\Filament\Resources\ReservationResource::class,
                CustomerResource::class,
                ServiceResource::class,
                SeatResource::class,
                RateResource::class,
                NotificationResource::class,
                WalletResource::class,
            ])
            ->darkMode(false)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
