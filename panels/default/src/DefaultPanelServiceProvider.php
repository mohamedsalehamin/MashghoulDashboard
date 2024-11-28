<?php

namespace App\DefaultPanel;

use App\CatalogModule\CatalogPlugin;
use App\CatalogModule\Resources\ConsultingReservationResource\Widgets\DoctorReservationsChart;
use App\CatalogModule\Resources\ConsultingReservationResource\Widgets\DoctorReservationsCountChart;
use App\CatalogModule\Resources\ConsultingReservationResource\Widgets\DoctorReservationsTotalsChart;
use App\CatalogModule\Resources\ConsultingReservationResource\Widgets\ReservationStats;
use App\CatalogModule\Resources\MedicalTestReservationResource\Widgets\LabReservationsCountChart;
use App\CatalogModule\Resources\MedicalTestReservationResource\Widgets\LabReservationsTotalsChart;
use App\ContentModule\ContentPlugin;
use App\DefaultPanel\Notifications\Notification;
use App\DefaultPanel\Pages\ResetPassword;
use App\DefaultPanel\Settings\GeneralSettings;
use App\ReportsModule\ReportsPlugin;
use App\CatalogModule\Resources\PatientResource\Widgets\PatientsChart;
use App\UsersModule\UsersPlugin;
use App\UtilitiesModule\UtilitiesPlugin;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification as BaseNotification;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\Permission\Models\Role;

class DefaultPanelServiceProvider extends PanelProvider {


    public function panel(Panel $panel): Panel {
        $this->app->bind(BaseNotification::class, Notification::class);

        return $panel
            ->default()
            ->font('cairo', 'https://fonts.googleapis.com/css2?family=Cairo:wght@700;800&display=swap')
            ->id('admin')
            ->path('admin')
            ->login()
            ->spa()
            ->passwordReset(resetAction: ResetPassword::class)
            ->userMenuItems([
                'profile' => MenuItem::make()->label(fn() => __('menu.edit_profile'))->url(fn() => route('filament.admin.pages.profile')),
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->navigationGroups([

                NavigationGroup::make()
                    ->label(fn(): string => __('menu.crm')),

                NavigationGroup::make()
                    ->label(fn(): string => __('menu.catalog')),

                NavigationGroup::make()
                    ->label(fn(): string => __('menu.employees')),

                NavigationGroup::make()
                    ->label(fn(): string => __('menu.content')),

                NavigationGroup::make()
                    ->label(fn(): string => __('menu.locations')),
                NavigationGroup::make()
                    ->label(fn(): string => __('menu.reports')),
                NavigationGroup::make()
                    ->label(fn(): string => __('menu.notifications')),

                NavigationGroup::make()
                    ->label(fn(): string => __('menu.settings')),

            ])
            ->colors([
                'primary' => Color::Indigo,
                'danger' => Color::Red,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->brandLogo(function (GeneralSettings $settings) {
//                return Storage::disk('public')->exists($settings->app_logo ?? 'null') ? asset("storage/$settings->app_logo") : 'https://awscdn1.tasawk.com/wp-content/uploads/2018/08/logo-d.png';

            })
            ->brandName(env("app_name"))
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->plugins([
                FilamentShieldPlugin::make(),
                SpatieLaravelTranslatablePlugin::make()->defaultLocales(['en', 'ar']),
                FilamentLanguageSwitchPlugin::make(),
                ThemesPlugin::make()->canViewThemesPage(fn() => auth()?->user()?->email === 'ahmed.mostafa.dev.eg@gmail.com'),
                CatalogPlugin::make(),
                ContentPlugin::make(),
                ReportsPlugin::make(),
                UsersPlugin::make(),
                UtilitiesPlugin::make(),
            ])
            ->databaseNotifications(false)
            ->sidebarCollapsibleOnDesktop()
            ->navigationItems([
                NavigationItem::make("roles")
                    ->label(fn(): string => __('filament-shield::filament-shield.nav.group'))
                    ->url('/admin/shield/roles')
                    ->icon('heroicon-o-users')
                    ->group(fn() => __('menu.settings'))
                    ->hidden(fn() => !auth()->user()->can('viewAny', Role::class))
                    ->sort(5),
            ])

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


            ])
            ->darkMode(false)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
