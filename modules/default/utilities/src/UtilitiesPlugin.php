<?php

namespace App\UtilitiesModule;

use Filament\Contracts\Plugin;
use Filament\Panel;

class UtilitiesPlugin implements Plugin {
    public static function make(): static {
        return app(static::class);
    }


    public function getId(): string {
        return 'tasawk-crm-module';
    }

    public function register(Panel $panel): void {
        $panel
            ->discoverResources(__DIR__ . '/Resources', 'App\\UtilitiesModule\\Resources')
            ->discoverWidgets(__DIR__ . '/Widgets', 'App\\UtilitiesModule\\Widgets')
            ->discoverPages(__DIR__ . '/Pages', 'App\\UtilitiesModule\\Pages');
    }

    public function boot(Panel $panel): void {
        // TODO: Implement boot() method.
    }
}
