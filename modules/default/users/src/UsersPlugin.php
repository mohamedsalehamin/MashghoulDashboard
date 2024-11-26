<?php

namespace App\UsersModule;

use Filament\Contracts\Plugin;
use Filament\Panel;

class UsersPlugin implements Plugin {
    public static function make(): static {
        return app(static::class);
    }


    public function getId(): string {
        return 'tasawk-catalog-module';
    }

    public function register(Panel $panel): void {
        $panel
            ->discoverResources(__DIR__ . '/Resources', 'App\\UsersModule\\Resources')
            ->discoverWidgets(__DIR__ . '/Widgets', 'App\\UsersModule\\Widgets')
            ->discoverPages(__DIR__ . '/Pages', 'App\\UsersModule\\Pages');
    }

    public function boot(Panel $panel): void {
        // TODO: Implement boot() method.
    }
}
