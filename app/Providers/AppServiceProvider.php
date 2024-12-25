<?php

namespace App\Providers;

use App\CatalogModule\Models\Reservation;
use App\ContentModule\Models\Page;
use App\DefaultPanel\Settings\LandingSettings;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Cache;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification as BaseNotification;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;
use App\DefaultPanel\Lib\Cart;
use App\DefaultPanel\Notifications\Notification;
use App\DefaultPanel\Settings\DeveloperSetting;
use App\DefaultPanel\Settings\GeneralSettings;
use Vite;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {

        $this->cart();
        view()->composer('*', function ($view,) {
            $settings = new GeneralSettings();
            $landingSettings = new LandingSettings();

            $pages = Cache::remember('pages', 60 * 60, function () use ($settings) {
                return collect($settings->app_pages)->mapWithKeys(function ($page, $pageName) {

                    return [$pageName => Page::find($page)];

                });
            });
            return $view
                ->with('landing_settings', $landingSettings)
                ->with('settings', $settings)
                ->with('social_links', $settings->social_links)
                ->with('locale', $this->app->getLocale())
                ->with('pages', $pages);
        });

        $this->app->bind(PaymentMyfatoorahApiV2::class, function () {
            return new PaymentMyfatoorahApiV2(
                config('myfatoorah.api_key'),
                config('myfatoorah.country_iso'),
                config('myfatoorah.test_mode')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        $this->app->bind(BaseNotification::class, Notification::class);
        $this->translateLabels();
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch->locales(['ar', 'en']);
        });
        $settings = new DeveloperSetting();
        view()->share('settings', new GeneralSettings());
        config()->set("app.debug", $settings->debug_mode);



        FilamentView::registerRenderHook(
            'panels::scripts.after',
            fn(): string => Blade::render('filament.firebase-initialization'),
        );
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn(): string => Blade::render('filament.hooks.head-end'),
        );

        FilamentAsset::register([
            Css::make('fontawesome', asset('https://pro.fontawesome.com/releases/v5.10.0/css/all.css')),
            Css::make('agora', asset('assets/css/agora.css')),
            Js::make('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js'),
        ]);

        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

    }


    private function translateLabels(): void {
        $translateLabelsComponents = [
            Field::class,
            Filter::class,
            SelectFilter::class,
        ];
        foreach ($translateLabelsComponents as $component) {
            $component::configureUsing(function ($c): void {
                $c->label(__('forms.fields.' . Str::afterLast($c->getName(), '.')));
            });
        }
        Field::macro('translatable', function () {
            return $this->hint(__('forms.fields.translatable'))
                ->hintIcon('heroicon-m-language');
        });

        Table::configureUsing(function (Table $table): void {
            $table->modifyQueryUsing(function (Builder $query): void {

                if ($query->getColumns()->getModel()->getCreatedAtColumn() && get_class($query->getColumns()->getModel()) !=Reservation::class) {

                    $query->latest($query->getColumns()->getModel()->getTable().".created_at");
                }

            });
        });

        TextEntry::configureUsing(function (TextEntry $field): void {
            $field->label(__('forms.fields.' . Str::replace('.', '_', $field->getName())));
        });

        Section::configureUsing(function (Section $section): void {
            $section
                ->collapsible()
                ->heading(__('sections.' . Str::lower($section->getHeading())));

        });
        Column::configureUsing(function ($c): void {
            $c->label(fn($column): string => __("forms.fields." . Str::replace('.', '_', Str::after($column->getName(), '.'))))
                ->translateLabel()
                ->toggleable();
        });

        \Filament\Infolists\Components\Section::configureUsing(function (\Filament\Infolists\Components\Section $section): void {
            $section->collapsible()->heading(__('sections.' . Str::lower($section->getHeading())));
        });


    }

    private function cart() {
        $this->app->singleton('cart', function ($app) {
            $storageClass = config('shopping_cart.storage');
            $eventsClass = config('shopping_cart.events');
            $storage = $storageClass ? new $storageClass() : $app['session'];
            $events = $eventsClass ? new $eventsClass() : $app['events'];
            $instanceName = 'cart';
            if (!session()->has('cart_id')) {
                session(['cart_id' => uniqid()]);
            }
            $session_key = session('cart_id');
            return new Cart(
                $storage,
                $events,
                $instanceName,
                $session_key,
                config('shopping_cart')
            );
        });
        app('events')->listen('cart.cleared', function ($cart) {
            /** @var Cart $coreCart */
            $coreCart = $this->app['cart'];
            session(['cart_id' => uniqid()]);
            $session_key = session('cart_id');
            $coreCart->session($session_key);
        });
    }
}
