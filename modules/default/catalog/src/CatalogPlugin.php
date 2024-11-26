<?php

namespace App\CatalogModule;

use Filament\Contracts\Plugin;
use Filament\Panel;

class CatalogPlugin implements Plugin {
    public static function make(): static {
        return app(static::class);
    }


    public function getId(): string {
        return 'tasawk-catalog-module';
    }

    public function register(Panel $panel): void {

        $panel
            ->discoverResources(__DIR__ . '/Resources', 'App\\CatalogModule\\Resources')
            ->discoverWidgets(__DIR__ . '/Widgets', 'App\\CatalogModule\\Widgets')
            ->discoverPages(__DIR__ . '/Pages', 'App\\CatalogModule\\Pages');
    }

    public function boot(Panel $panel): void {
        // TODO: Implement boot() method.
    }

    public function getLogo() {
        return asset('as');
    }

    public function getName(): string {
        return "Catalog";
    }

    public function description() {

    }

    public function version() {
        return "1.0.2";
    }
}
