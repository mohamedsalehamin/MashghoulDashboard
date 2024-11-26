<?php

namespace App\ReportsModule;

use Filament\Contracts\Plugin;
use Filament\Panel;

class ReportsPlugin implements Plugin {
    public static function make(): static {
        return app(static::class);
    }


    public function getId(): string {
        return 'tasawk-crm-module';
    }

    public function register(Panel $panel): void {
        $panel
            ->discoverResources(__DIR__ . '/Resources', 'App\\ReportsModule\\Resources')
            ->discoverWidgets(__DIR__ . '/Widgets', 'App\\ReportsModule\\Widgets')
            ->discoverPages(__DIR__ . '/Pages', 'App\\ReportsModule\\Pages');
    }

    public function boot(Panel $panel): void {
        // TODO: Implement boot() method.
    }
}
