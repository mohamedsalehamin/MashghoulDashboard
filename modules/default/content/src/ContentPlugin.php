<?php

namespace App\ContentModule;

use Filament\Contracts\Plugin;
use Filament\Panel;

class ContentPlugin implements Plugin {
    public static function make(): static {
        return app(static::class);
    }


    public function getId(): string {
        return 'tasawk-content-module';
    }

    public function register(Panel $panel): void {
        $panel
            ->discoverResources(__DIR__ . '/Resources', 'App\\ContentModule\\Resources')
            ->discoverWidgets(__DIR__ . '/Widgets', 'App\\ContentModule\\Widgets')
            ->discoverPages(__DIR__ . '/Pages', 'App\\ContentModule\\Pages');
    }

    public function boot(Panel $panel): void {
        // TODO: Implement boot() method.
    }
}
